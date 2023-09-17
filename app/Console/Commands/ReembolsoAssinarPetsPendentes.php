<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Clientes;
use App\Models\Pets;
use App\Http\Util\Superlogica\Plans;
use Illuminate\Support\Facades\{Log,Mail};
use Carbon\Carbon;

class ReembolsoAssinarPetsPendentes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reembolso:assinar_pets_pendentes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Faz o processo de assinatura no Superlógica pra todos pets pendentes';

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
        $falhaAssinatura = false;

        try {
            $clientes = Clientes::where('adesao_reembolso', 'WEBSITE')
            ->where('assinatura_superlogica_reembolso', 'PENDENTE')
            ->get();
        
            foreach($clientes as $cliente) {
                $pets = $cliente->pets()
                    ->where('reembolso', 1)
                    ->where('assinatura_superlogica_reembolso', 0)
                    ->get();

                $taxaAdesao = $cliente->taxa_adesao_reembolso == 'S' ? true : false;
                foreach($pets as $pet) {

                    $assinatura = $this->assinarSuperlogica($pet, $cliente->id_externo, $taxaAdesao);

                    if(!$assinatura || ($assinatura['signed'] == false)) {
                        $falhaAssinatura = true;
                    }

                    $taxaAdesao = false;
                }
                
                $plansManager = new Plans();
                $pagamentos = $plansManager->getCharge($cliente->id_externo);

                if(isset($pagamentos)) {
                    foreach($pagamentos as $pagamento) {
                        $pet = Pets::where('id_externo', $pagamento->id_adesao_plc)->first();
                        if(!isset($pet)) {
                            continue;
                        }

                        $pet->id_cobranca_externa_reembolso = $pagamento->id_recebimento_recb;
                        $pet->save();
                    }
                }

                if($falhaAssinatura == false) {
                    $cliente->assinatura_superlogica_reembolso = 'CADASTRADO';
                    $cliente->save();
                    //$this->enviarEmailConfirmacao($cliente);
                }

                if($falhaAssinatura == true) {
                    $mail = Mail::send('mail.reembolso.erro_superlogica', ['dados' => ['id' => $cliente->id, 'nome' => $cliente->nome_cliente, 'data_cadastro' => Carbon::now(), 'info' => 'O cliente foi cadastrado no superlógica mas o Superlógica se recusou a cadastrar os seus pets. Favor pesquisá-lo no ERP.']], function($message) {
                        $message->to('breno.grillo@vixsolution.com');
                        $message->subject('ADESÃO REEMBOLSO');
                        $message->from('noreply@lifepet.com.br', 'Lifepet');
                    });
                }
                
            }

        } catch (\Exception $e) {
            Log::error('ReembolsoAssinarPetsPendentes: ' . $e->getMessage());
        }
    }
}
