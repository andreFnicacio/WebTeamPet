<?php

namespace App\Models;

use App\Email;
use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\Financeiro\FinanceRepository;
use App\Helpers\API\Financeiro\Services\Customer as CustomerFinance;
use App\Helpers\Utils;
use App\Http\Util\LogEvent;
use App\Http\Util\Logger;
use App\Http\Util\LogPriority;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Guides\Entities\HistoricoUso;

/**
 * Class Clientes
 * @package App\Models
 * @property $email
 * @property $nome_cliente
 * @property $celular
 * @property $data_nascimento
 * @property $rg
 * @property $cpf
 * @property $sexo
 * @property $forma_pagamento
 * @property bool $ativo
 * @property User $user
 * @property int id
 * @property int $id_superlogica
 * @property int $new_superlogica_id
 * @property Pets|null $pets
 * @property Carbon|null $last_sync
 * @property int $financial_id
 *
 * @method static cpf($cpf)
 */
class Clientes extends Model\Model
{
    use SoftDeletes;

    public $table = 'clientes';

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const FORMA_PAGAMENTO_CARTAO = 'cartao';
    const FORMA_PAGAMENTO_BOLETO = 'boleto';
    const FORMA_PAGAMENTO_PIX = 'pix';
    const FORMA_PAGAMENTO_DESCONTO_EM_FOLHA = 'desconto_folha';

    const DOCUMENTOS_OBRIGATORIOS = [
        'rg_cnh_frente' => 'RG ou CNH (frente)',
        'rg_cnh_verso' => 'RG ou CNH (verso)',
        'comprovante_residencia' => 'Comprovante de Residência',
    ];

    public $ignoreCpfMutator = false;

    protected $dates = ['deleted_at', 'last_sync', 'credit_card_added_at'];
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'nome_cliente',
        'email',
        'cpf'
    ];

    public $fillable = [
        'nome_cliente',
        'cpf',
        'rg',
        'data_nascimento',
        'numero_contrato',
        'cep',
        'rua',
        'numero_endereco',
        'complemento_endereco',
        'bairro',
        'cidade',
        'estado',
        'telefone_fixo',
        'celular',
        'email',
        'ativo',
        'id_externo',
        'sexo',
        'estado_civil',
        'observacoes',
        'participativo',
        'valor',
        'vencimento',
        'id_conveniado',
        'id_usuario',
        'primeiro_acesso',
        'aprovado',
        'participativo',
        'grupo',
        'senha_plano',
        'token_firebase',
        'token',
        'adesao_reembolso',
        'taxa_adesao_reembolso',
        'assinatura_superlogica_reembolso',
        'ip',
        'dia_vencimento',
        'forma_pagamento',
        'new_superlogica_id',
        'financial_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'nome_cliente' => 'string',
        'cpf' => 'string',
        'rg' => 'string',
        'data_nascimento' => 'date',
        'numero_contrato' => 'string',
        'cep' => 'string',
        'rua' => 'string',
        'numero_endereco' => 'string',
        'complemento_endereco' => 'string',
        'bairro' => 'string',
        'cidade' => 'string',
        'estado' => 'string',
        'telefone_fixo' => 'string',
        'celular' => 'string',
        'email' => 'string',
        'ativo' => 'boolean',
        'id_externo' => 'integer',
        'sexo' => 'string',
        'estado_civil' => 'string',
        'observacoes' => 'string',
        'primeiro_acesso' => 'boolean',
        'senha_plano' => 'string',
        'token_firebase' => 'string',
        'dia_vencimento' => 'integer',
        'forma_pagamento' => 'string',
        'financial_id' => 'integer'
    ];

    protected $appends = [
        'primeiro_nome_cliente'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    
    const PAGAMENTO_EM_DIA = "Em dia";

    const INADIMPLENTE_60_DIAS = "INADIMPLENTE +60 DIAS";

    const EM_ATRASO = "EM ATRASO";


    public function getPrimeiroNomeClienteAttribute() {
        return head(explode(' ', trim($this->nome_cliente)));
    }

    public function setDataNascimentoAttribute($value) {

        if(strlen($value) == strlen("DD/MM/AAAA")) {
            $this->attributes['data_nascimento'] = Carbon::createFromFormat('d/m/Y', $value)->format(Utils::UTC_DATE);
        } else if (strlen($value) == strlen("DD/MM/AAAA HH:ii:ss")) {
            $this->attributes['data_nascimento'] = Carbon::createFromFormat('d/m/Y H:i:s', $value)->format(Utils::UTC_DATE);
        } else {
            date('Y-m-d', strtotime($value));
        }
    }

    public function setCelularAttribute($value) {

        $this->attributes['celular'] = preg_replace('/\D+/', '', $value);
    }



    public function setCpfAttribute($value) {
        $this->attributes['cpf'] = $value;
        if(!$this->ignoreCpfMutator) {
            $this->attributes['cpf'] = preg_replace('/\D+/', '', $value);
        }
        $this->attributes['senha_plano'] = substr(preg_replace('/\D+/', '', $value), 0, 4);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function cobrancas()
    {
        return $this->hasMany(\App\Models\Cobrancas::class, 'id_cliente', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     **/
    public function pets()
    {
        return $this->hasMany(\App\Models\Pets::class, 'id_cliente', 'id');
    }

    public function notas()
    {
        return $this->hasMany(\App\Models\Notas::class, 'cliente_id');
    }
    public function sfIntegration()
    {
        return $this->hasOne(\App\Models\Integration\SfIntegration::class, 'id_cliente');
    }

    public function addNota($corpo, $user = null)
    {
        if(!$user) {
            $user = 1;
        }

        return $this->notas()->create([
            'cliente_id' => $this->id,
            'corpo' => $corpo,
            'user_id' => $user,
        ]);
    }

    public function conveniado()
    {
        return $this->belongsTo(\App\Models\Conveniados::class, 'id_conveniado', 'id');
    }

    public function contasBancarias()
    {
        return $this->hasMany(\App\Models\ClientesContasBancarias::class, 'id_cliente', 'id');
    }

    public function documentos()
    {
        return $this->hasMany(\App\Models\DocumentosClientes::class, 'id_cliente', 'id');
    }

    public function emails()
    {
        return $this->morphMany(Email::class, 'emailable');
    }

    public function getNomeCurto()
    {
        $exploded = explode(' ', $this->nome_cliente);
        return $exploded[0] . (isset($exploded[1]) ? ' '.$exploded[1] : '');
    }

    public function recorrencias()
    {
        if(!$this->id_externo) {
            return [];
        }
        $c = new \App\Helpers\API\Superlogica\Client();
        return collect($c->recorrencias($this->id_externo));
    }

    public function assinaturas()
    {
        if(!$this->id_externo) {
            return [];
        }
        $c = new \App\Helpers\API\Superlogica\Client();
        return collect($c->assinaturas(["identificadorCliente" => $this->id_externo]));
    }

    public function totalRecebido()
    {
        return $this->cobrancas()->sum('vl_total_recb');
    }

    public function totalDevido()
    {
        return $this->recorrencias()->sum('total');
    }

    public function getStatusPagamentoAttribute()
    {
        return $this->statusPagamento();
    }

    public function uploads()
    {
        return \App\Models\Uploads::where('bind_with', '=', 'clientes')
                                    ->where('binded_id', '=', $this->id);
    }

    public function indicacoes()
    {
        return \App\Models\Indicacoes::where('id_cliente', $this->id);
    }

    public function hasUser()
    {
        return !empty($this->id_usuario) ||
               \App\User::where('email', $this->email)->exists();

    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function hasSegundoResponsavel()
    {
        return !empty($this->segundo_responsavel_nome);
    }

    public function planos()
    {
        $pets = $this->pets()->get();
        $planos = [];
        foreach($pets as $pet) {
            $planos[] = $pet->plano();
        }

        return $planos;
    }

    public function documentosPlano()
    {
        /**
         * @var Planos[] $planos
         */
        $planos = $this->planos();
        $documentos = collect([]);
        foreach($planos as $p) {
            $documentosPlano = $p->documentosPublicos()->get();
            if (count($documentosPlano)) {
                $documentos = $documentos->merge($documentosPlano);
            }
            $documentosPlano = [];
        }

        return $documentos;
    }

    public function encaminhamentos()
    {
        $pets = $this->pets()->get();

        return \Modules\Guides\Entities\HistoricoUso::where('status', HistoricoUso::STATUS_LIBERADO)
                                       //->where('data_liberacao', '<=', (new Carbon()))
                                       ->whereIn('id_pet', $pets->pluck('id'))
                                       ->where('tipo_atendimento', HistoricoUso::TIPO_ENCAMINHAMENTO)
                                       ->whereNull('realizado_em')
                                       ->groupBy('numero_guia')
                                       ->get();
    }

    public function statusPagamento($atrasoMaximo = 4)
    {
        $cobrancas = $this->cobrancas()->with('pagamentos')->where('status', 1)->whereNull('cancelada_em')->get();

        $status = [
            'vencidas' => []
        ];

        foreach($cobrancas as $c) {

            if($c->pagamentos->count() > 0) {
                if($c->pagamentos->sum('valor_pago') >= 0) {
                    continue;
                }
            } else {
                $today = new Carbon();
                if($today->gt($c->data_vencimento->addDays($atrasoMaximo))) {
                    $status['vencidas'][] = $c;
                }
            }
        }
        if(empty($status['vencidas'])) {
            return self::PAGAMENTO_EM_DIA;
        } else {
            foreach($status['vencidas'] as $v) {
                if($v->data_vencimento->diffInDays(new Carbon()) >= 60) {
                    return self::INADIMPLENTE_60_DIAS;
                }
            }

            return self::EM_ATRASO;
        }
    }

    public function cobrancasAtrasadas($atrasoMaximo = 4)
    {
        $cobrancas = $this->cobrancas()->with('pagamentos')->where('status', 1)->whereNull('cancelada_em')->get();

        $status = [
            'vencidas' => []
        ];

        foreach($cobrancas as $c) {

            if($c->pagamentos->count() > 0) {
                if($c->pagamentos->sum('valor_pago') >= 0) {
                    continue;
                }
            } else {
                $today = new Carbon();
                if($today->gt($c->data_vencimento->addDays($atrasoMaximo))) {
                    $status['vencidas'][] = $c;
                }
            }
        }

        return count($status['vencidas']);
    }

    public function getPropostas()
    {
        $dados_proposta = [];
        if ($this->dados_proposta) {
            $dados_proposta = json_decode($this->dados_proposta);
        }
        return $dados_proposta;
    }

    public function getCobrancasEmAberto(){
        $cobrancas = $this->cobrancas()->get()->filter(function ($cob) {
            return $cob->pagamentos()->count() === 0;
        });
        return $cobrancas;
    }

    public function checkSenhaPlano($senha){
        if ($senha == $this->senha_plano) {
            return true;
        }
        return false;
    }

    public function assinarFichaAvaliacao($id_ficha, $senha_plano)
    {
        if ($id_ficha) {

            $ficha = (new \Modules\Veterinaries\Entities\FichasAvaliacoes)::find($id_ficha);
            $cliente = $this;

            if ($senha_plano) {
                if ($ficha) {
                    if ($cliente->checkSenhaPlano($senha_plano)) {
                        $guias = (new HistoricoUso())->where('numero_guia', $numero_guia)->get()->map(function ($guia) use ($meio) {
                            $guia->gerarAssinaturaCliente($meio);
                        });
                        $data = [
                            'status' => true,
                            'http' => 200,
                            'msg' => 'Guia assinada com sucesso!'
                        ];
                    } else {
                        $data = [
                            'status' => false,
                            'http' => 401,
                            'msg' => 'A Senha não confere! A guia não foi assinada!'
                        ];
                    }
                } else {
                    $data = [
                        'status' => false,
                        'http' => 401,
                        'msg' => 'A guia não foi encontrada!'
                    ];
                }
            } else {
                $data = [
                    'status' => false,
                    'http' => 401,
                    'msg' => 'A Senha é obrigatória!'
                ];
            }
        } else {
            $data = [
                'status' => false,
                'http' => 401,
                'msg' => 'O número da guia é obrigatório!'
            ];
        }
        return $data;
    }

    public function assinarGuia($numero_guia, $senha_plano, $meio = 1)
    {
        if ($numero_guia) {

            $hu = HistoricoUso::where('numero_guia', $numero_guia)->first();
            $cliente = $this;

            if ($senha_plano) {
                if ($hu) {
                    if ($cliente->checkSenhaPlano($senha_plano)) {
                        $guias = (new HistoricoUso())->where('numero_guia', $numero_guia)->get()->map(function ($guia) use ($meio) {
                            $guia->gerarAssinaturaCliente($meio);
                        });
                        $data = [
                            'status' => true,
                            'http' => 200,
                            'msg' => 'Guia(s) assinada(s) com sucesso!'
                        ];
                    } else {
                        $data = [
                            'status' => false,
                            'http' => 401,
                            'msg' => 'A Senha não confere! A guia não foi assinada!'
                        ];
                    }
                } else {
                    $data = [
                        'status' => false,
                        'http' => 401,
                        'msg' => 'A guia não foi encontrada!'
                    ];
                }
            } else {
                $data = [
                    'status' => false,
                    'http' => 401,
                    'msg' => 'A Senha é obrigatória!'
                ];
            }
        } else {
            $data = [
                'status' => false,
                'http' => 401,
                'msg' => 'O número da guia é obrigatório!'
            ];
        }
        return $data;
    }

    public function carteiraDigitalTransacoes() {
        return $this->hasMany(\App\Models\CarteiraDigital\CarteiraDigitalTransacao::class, 'cliente_id', 'id');
    }

    public function carteiraDigitalSaldo() {

        $debitos = $this->carteiraDigitalTransacoes()->where('tipo', 1)->sum('valor');
        $creditos = $this->carteiraDigitalTransacoes()->where('tipo', 2)->sum('valor');

        return $creditos - $debitos;
    }

    /**
     * Retorna os dados necessários para criar um registro de cliente no Sistema Financeiro
     * @return array
     */
    public function financeData() {
        $data = [];
        $data['name'] = $this->nome_cliente;
        $data['email'] = $this->email;
        $data['cep'] = $this->cep;
        $data['street'] = $this->rua;
        $data['address_number'] = $this->numero_endereco;
        $data['neighbourhood'] = $this->bairro;
        $data['city'] = $this->cidade;
        $data['state'] = $this->estado;
        $data['cpf'] = $this->cpf;

        return $data;
    }

    public function getFinanceCustomer() {
        $financeiro = new Financeiro();
        return $financeiro->createBasicCustomer($this->financeData());
    }

    public function addIntegrationSF()
    {
        return $this->sfIntegration()->firstOrCreate([
            'id_cliente' => $this->id,
        ]);
    }
    /**
     *  Sync finanenceiro
     *  Executado por Job
     *
     */
    public function syncFinance()
    {
        $check_errors = true;
        $logger = new Logger('clientes', $this->table);
        $customerFinance = new CustomerFinance;
        $financeRepository = new FinanceRepository($this, $logger);

        $sfIntegration = $this->sfIntegration;
        if ($sfIntegration === null)
        {
            $sfIntegration = $this->addIntegrationSF();
        }
        $sfIntegration->last_sync_at = Carbon::now();
        $sfIntegration->save();
        /**
         *  Verifica se possui id_externo e busca no SF com id_externo
         * Se não possuir, tenta buscar pelo CPF
         */
        if ($this->id_externo !== null)
        {
            /**
             * Caso $check_erros seja true, verifica se há cadastro no erp com o CPF,
             * OBS: caso não encontre, cadastro está incorreto no SF
             */
            if ($check_errors) {

                $customer_cpf = $financeRepository->checkExistsCustomerCPF();
                if (!$customer_cpf)
                {
                   // $this->id_externo = null;
                  //  $this->save();
                 //   dd($customer_cpf);
                    $sfIntegration->save();
                    return false;
                }
            }
            /**
             * Busca o cliente pelo refcode
             */
          $customer = $financeRepository->checkCustomerByRefCode();

        } else {

            $customer = $financeRepository->getCustomerByDocument();

        }

        /**
         * caso verifique os erros acima, verifica se a conta que achou com o CPF é a mesma para o id_externo
         * Caso não for, para a sincronização, pois há um possível erro no cadastro
         */
        if (isset($customer_cpf) && $customer_cpf->body->id !== $customer->body->id)
        {
            $logger->register(
                LogEvent::NOTICE,
                LogPriority::HIGH,
                "O cadastro do cliente {$this->nome_cliente} há algum erro no Sistema Financeiro. \n",
                $this->id);


            $sfIntegration->save();
            return false;
        }

        /**
         * Inicia payload do customer e preenche os dados de acordo com o Cliente
         */
        $payload = new \stdClass;
        $payload->name = $this->nome_cliente;
        $payload->lastname = '';
        $payload->status = $this->ativo ? 'A' : 'I';
        $payload->payment_type = $this->forma_pagamento == 'cartao' ? 'creditcard' : 'boleto';
        $payload->payment_type = $this->forma_pagamento == 'desconto_folha' ? 'free' : $payload->payment_type;
        $payload->financial_status = 1;
        $payload->due_day = $this->dia_vencimento;
        $payload->gender = $this->sexo;
        $payload->cpf_cnpj = $this->cpf;
        $payload->birthdate = $this->data_nascimento ? $this->data_nascimento->format('Y-m-d') : null;
        $payload->identity = $this->rg;


        /**
         *  Verifica no get do customer, se já existe ou não,
         *  caso exista, da update com o id que veio da consulta,
         *  senão cria um novo
         */
        if ($financeRepository->verifyStatusCode($customer))
        {
            $customer = $customerFinance->update($customer->body->id, $payload);
        } else {
            $customer = $customerFinance->create($payload);

            $logger->register(
                LogEvent::NOTICE,
                LogPriority::MEDIUM,
                "O cadastro do cliente {$this->nome_cliente} foi efetuado no Sistema Financeiro. \n",
                $this->id);

        }

        /**
         * POST do customer do financeiro sem sucesso.
         */
        if (!$financeRepository->verifyStatusCode($customer))
        {
            $sfIntegration->error_customer = Carbon::now();
            $message = "O cadastro do cliente {$this->nome_cliente} não possui cadastro Sistema Financeiro. \n".json_encode($customer);
            $financeRepository->saveLog('NOTICE', 'HIGH', $message, $this->id);
            $sfIntegration->save();
            return false;

        }
        /**
         * Se não possui id_externo no cliente,
         * define de acordo com o financeiro e salva
         */
        if ($this->id_externo != $customer->body->ref_code)
        {

            $this->id_externo = $customer->body->ref_code;

            $this->save();

        }

        /**
         * Check Address
         * Verifica se a consulta no financeiro do customer possui endereço
         * Caso tenha pega o id que possui no financeiro e monta o payload de endereço para atualizar
         * Obs: caso tenha mais de um endereço, ele vai usar o do ultimo
         * Caso precise mudar, adicione um continue antes de finalizar o foreach
         */
        $addresses = new \stdClass();
        foreach ($customer->body->address as $address)
        {
            $addresses->id = $address->id;
        }
        $addresses->address1 = $this->rua;
        $addresses->address2 = $this->bairro;
        $addresses->number = $this->numero_endereco;
        $addresses->state = $this->estado;
        $addresses->country = 'Brasil';
        $addresses->zipcode = $this->cep;
        $addresses->city = $this->cidade;
        $addresses->ibge = isset($address, $address->ibge) ? $address->ibge : null;
        /**
         * Caso ibge seja nulo, remove ibge do payload
         */
        if ($addresses->ibge === null)
            unset($addresses->ibge);

        /**
         * Adiciona o payload do endereço no payload do usuário
         */
        $payload->address = [];
        $payload->address[] = $addresses;


        /**
         * Verifica as assinaturas do pet e faz update ou cria no sistema financeiro
         */
        $subscriptions = $financeRepository->updateSubscriptions($customer);


        /**
         * Faz update no cliente com o endereço no campo
         */
        $customer_update = $customerFinance->update($customer->body->id, $payload);
        if ($financeRepository->verifyStatusCode($customer_update)) {

            $sfIntegration->sync_at = $sfIntegration->sync_at === null ? Carbon::now() : $sfIntegration->sync_at;

            $logger->register(
                LogEvent::NOTICE,
                LogPriority::MEDIUM,
                "O cadastro do cliente {$this->nome_cliente} foi atualizado no Sistema Financeiro. \n",
                $this->id);
        } else {

            $sfIntegration->error_customer = Carbon::now();

            $logger->register(
                LogEvent::NOTICE,
                LogPriority::HIGH,
                "O cadastro do cliente {$this->nome_cliente} teve um erro ao atualizar com o Sistema Financeiro. \n".json_encode($customer_update),
                $this->id);
        }

        $sfIntegration->save();
      //  dd($this->sfIntegration, 'ae');
     //   dd($payload, $customer_update, $customer, $customer_old);
        return ['message'=> ['Cliente sincronizado com sucesso.']];
    }

    public function syncWithFinance()
    {
        return $this->syncFinance();
        $logger = new Logger('clientes', 'clientes');
        $financeiro = new Financeiro();
        $customerFinance = new CustomerFinance;
        if ($this->id_externo !== null)
        {
            $customer = $customerFinance->getByRefCode($this->id_externo);
        } else {
            $customer = $customerFinance->getByDocument($this->cpf);
        }
        $update = false;
        if($customer) {
            $form = new \stdClass;
            if(!$this->id_externo || $this->id_externo !== $customer->ref_code) {
                $this->id_externo = $customer->ref_code;
                $update = true;
            }

            if(!$this->dia_vencimento) {
                $this->dia_vencimento = $customer->due_day;
                $update = true;
            }

            if(!$this->forma_pagamento) {
                $this->forma_pagamento = ($customer->payment_type == 'boleto' ? 'boleto' : 'cartao');
                $update = true;
            }


            if ($customer->name != $this->nome_cliente)
            {
                $form->name = $this->nome_cliente;
                $update = true;

            }
            if($update) {
                $this->update();
                $form->id = $this->id_externo;
                $customerFinance->update($form);
                $logger->register(LogEvent::NOTICE, LogPriority::MEDIUM, "O cadastro do cliente {$this->nome_cliente} sincronizado com o Sistema Financeiro. \n", $this->id);
            }
        } else {
            //Cadastra cliente no SF
            try {
                $customer = $financeiro->createCustomer([
                    'nome_cliente' => $this->nome_cliente,
                    'email' => $this->email,
                    'cpf' => $this->cpf,
                    'status' => 'A',
                    'dia_vencimento' => $this->dia_vencimento,
                    'forma_pagamento' => $this->forma_pagamento,
                    'sexo' => $this->sexo,
                    'cep' => $this->cep,
                    'rua' =>  $this->rua,
                    'numero_endereco' => $this->numero_endereco,
                    'bairro' => $this->bairro,
                    'cidade' =>  $this->cidade,
                    'estado' =>  $this->estado,
                    'id_externo' => $this->id,
                ]);

                $this->id_externo = $customer->ref_code;
                $this->update();
                $logger->register(LogEvent::NOTICE, LogPriority::MEDIUM, "O cliente {$this->nome_cliente} foi cadastrado no Sistema Financeiro.", $this->id);
                $logger->register(LogEvent::NOTICE, LogPriority::MEDIUM, "O cadastro do cliente {$this->nome_cliente} sincronizado com o Sistema Financeiro.", $this->id);
            } catch (Exception $e) {
                $logger->register(LogEvent::WARNING, LogPriority::MEDIUM, "Não foi possível realizar a sincronia do cliente com o Sistema Financeiro. Erro: {$e->getMessage()}", $this->id);
            }
        }
    }

    public function scopeAtivo($query)
    {
        return $query->where('ativo', 1);
    }

    public function scopeMembroDesde($query, $data)
    {
        return $query->where('created_at', '>=', $data);
    }

    public function scopeCpf($query, $cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        return $query->where('cpf', '=', $cpf);
    }

    public static function getDDD($phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if(strlen($phone) <= 8) {
            return null;
        }

        $ddd = "0" . substr($phone, 0, 2);
        return $ddd;
    }

    public static function getPhoneWithoutDDD($phone) 
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if(strlen($phone) <= 8) {
            return $phone;
        }

        return substr($phone, 2, strlen($phone) - 2);
    }

    public static function getFirstEmail($email) 
    {
        $string = $email;
        $pattern = '/[a-z0-9_\-\+\.]+@[a-z0-9\-]+\.([a-z]{2,4})(?:\.[a-z]{2})?/i';
        preg_match_all($pattern, $string, $matches);

        return isset($matches[0]) ? $matches[0][0] : null;
    }

    public function getNumericCpfAttribute()
    {
        return preg_replace('/[^0-9]/', '', $this->cpf);
    }

    public function getTiposPets() 
    {
        $tipos = $this->pets->pluck('tipo')->toArray();

        if(empty($tipos)){
            return 'NENHUM';
        }

        $possuiGato = in_array("GATO", $tipos);
        $possuiCachorro = in_array("CACHORRO", $tipos); 

        if($possuiGato && $possuiCachorro){
            return 'AMBOS';
        }
        return $tipos[0];
    }

    public function loggable(): array
    {
        return [
            'id' => $this->id,
            'nome_cliente' => $this->nome_cliente,
            'cpf' => $this->cpf,
            'celular' => $this->celular,
            'email' => $this->email,
            'ativo' => $this->ativo,
            'status' => $this->statusPagamento
        ];
    }

    public static function hasActivePet()
    {
        return \DB::table('clientes')
                ->join('pets', 'pets.id_cliente', '=','clientes.id')
                ->where('pets.ativo', '=', 1)
                ->count() > 0;
    }

    public function getIdSuperlogicaAttribute($value)
    {
        return $this->new_superlogica_id ?: $value;
    }
}
