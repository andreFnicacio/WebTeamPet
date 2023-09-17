<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clientes;
use App\Models\Pets;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Http\Util\Superlogica\Plans;

class ReembolsoVerificarProcessamentoCartao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reembolso:verificar_processamento_cartao';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica os pagamentos que ainda não foram processados no Superlógica e envia e-mail com resposta ao cliente';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {

        
            $pets = Pets::where('reembolso', 1)
                ->where('reembolso', 1)
                ->where('status_primeiro_pag_reembolso', 'PENDENTE')
                ->whereNotNull('id_cobranca_externa_reembolso')
                ->where('id_cobranca_externa_reembolso', '>', 0)
                ->get();
                
    
            if($pets->isEmpty()) {
                Log::info('ReembolsoVerificarProcessamentoCartao: Nenhum pet encontrado');
            }

        
            $clientes = [];
            $plans = new Plans();
            foreach($pets as $pet) {

                // Se já nao foi atribuido, inicia array do cliente especifico
                if(!isset($clientes[$pet['id_cliente']])) {
                    $clientes[$pet['id_cliente']] = [
                        'pag_primeiros' => 0,
                        'pag_sucessos' => 0,
                        'pag_falhas' => 0,
                        'pag_ja_verificados' => 0,
                        'pets' => []
                    ];
                }

                // Pega o ID da cobrança e busca na SUPERLÓGICA
                $idRecebimentoRecb = $pet['id_cobranca_externa_reembolso'];
                $pagamento = $plans->getChargeById($idRecebimentoRecb);
                
                // Se não existir pagamento no SUPERLÓGICA, pula o pet
                if(!isset($pagamento)) {
                    continue;
                }

                $pagamento = $pagamento[0];

                // Se o pagamento for diferente de "Primeiro pagamento" pula o pet
                if(!isset($pagamento->st_observacaoexterna_recb) || $pagamento->st_observacaoexterna_recb != 'Primeiro pagamento') {
                    continue;
                }

                $clientes[$pet['id_cliente']]['pag_primeiros'] += 1;
                // Anexa ao array o pet e soma como primeiro pagamento
                $clientes[$pet['id_cliente']]['pets'][$pet['id']]['data_cobranca_externa_reembolso'] = $pagamento->dt_alteracao_recb;
                $clientes[$pet['id_cliente']]['pets'][$pet['id']]['ja_verificado'] = false;

                // Se a data da alteração da cobrança for igual a data salva no banco é adicionado na flag de ja verificados
                if($pagamento->dt_alteracao_recb == $pet['data_cobranca_externa_reembolso']) {
                    $clientes[$pet['id_cliente']]['pag_ja_verificados'] += 1;
                    $clientes[$pet['id_cliente']]['pets'][$pet['id']]['ja_verificado'] = true;
                }

                // Se houver erro no cartão é somado aos pagamentos com falhas
                if(!empty($pagamento->st_errocartao_recb ?? NULL)) {
                    $clientes[$pet['id_cliente']]['pag_falhas'] += 1;
                    continue;
                }

                // Se houver data de liquidação é somado aos pagamentos com sucesso
                if(!empty($pagamento->dt_liquidacao_recb ?? NULL)) {
                    $clientes[$pet['id_cliente']]['pag_sucessos'] += 1;
                    continue;
                }

            }

        
            if(empty($clientes)) {
                Log::info('ReembolsoVerificarProcessamentoCartao Nenhum cliente pra verificar'); 
            }

            foreach($clientes as $id => $cliente) {
                // Verifica se os pagamentos dos pets do cliente são "Primeiro pagamento"
                if($cliente['pag_primeiros'] == 0) {
                    continue;
                }
                
                // Verifica se a quantidade de sucessos é a mesma que o número de primeiro pagamentos
                if($cliente['pag_sucessos'] == $cliente['pag_primeiros']) {

                    // Por segurança verifica se há pets do cliente pra considerar
                    if(!isset($cliente['pets'])) {
                        continue;
                    }

                    // Nula a variável para evitar conflito
                    $petObj = null;
                    
                    // Percorre os pets verificados
                    foreach($cliente['pets'] as $petId => $pet) {

                        if($pet['ja_verificado'] > 0) {
                            continue 2;
                        }

                        // Busca o pet verificado na base de dados
                        $petObj = Pets::find($petId);

                        // Se não foi encontrado o pet, pula para o próximo
                        if(!isset($petObj)) {
                            continue;
                        }

                        // Se foi encontrado, marca o mesmo como SUCESSO 
                        $petObj->status_primeiro_pag_reembolso = 'SUCESSO';
                        $petObj->data_cobranca_externa_reembolso = $pet['data_cobranca_externa_reembolso'];
                        $petObj->data_primeiro_pag_reembolso = Carbon::now();
                        $petObj->ativo = true;
                        $petObj->save();
                    }

                    // Nula a variável pra evitar conflito
                    $clienteObj = null;

                    // Busca o cliente na base de dados
                    $clienteObj = Clientes::find($id);
                    
                    // Se o cliente não for encontrado, pula para o próximo
                    if(!$clienteObj) {
                        continue;
                    }


                    // Marca o cliente para inativo e renova a token
                    $clienteObj->ativo = true;
                    $clienteObj->token = Str::random(30);
                    $clienteObj->save();
                    
                    // Envia o e-mail para o cliente indicando sucesso/boas vindas
                    $this->enviarEmailBoasVindas($clienteObj);
                }
            }
        } catch (\Exception $e) {
            Log::error('ReembolsoVerificarProcessamentoCartao: ' . $e->getMessage());
        }
    }
}
