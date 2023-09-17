<?php

namespace App\Http\Controllers\API\Traits;

use Illuminate\Http\Request;
use App\Http\Controllers\AreaClienteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Util\Superlogica\Client;
use App\Http\Util\Superlogica\Plans;
use App\Helpers\Utils;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Models\{Clientes,Pets,Role};
use Carbon\Carbon;
use Validator;

trait IntegralAPITrait {

	private $producao = true;

	public function cadastrarCliente($dados) {
		$cliente = new Clientes();
		$cliente->fill([
			'nome_cliente' => $dados['nome'] . ' ' . $dados['sobrenome'],
			'cpf' => preg_replace( '/[^0-9]/', '', $dados['cpf'] ),
			'rg' => isset($dados['rg']) ? $dados['rg'] : '',
			'data_nascimento' => $dados['data_nascimento'],
			'estado_civil' => $dados['estado_civil'],
			'celular' => $dados['celular'],
			'cep' => preg_replace( '/[^0-9]/', '', $dados['cep'] ),
			'rua' => $dados['rua'],
			'numero_endereco' => $dados['numero_endereco'],
			'complemento_endereco' => isset($dados['complemento_endereco']) ? $dados['complemento_endereco'] : '',
			'bairro' => $dados['bairro'],
			'cidade' => $dados['cidade'],
			'estado' => $dados['estado'],
			'email' => $dados['email'],
			'ativo' => 0,
			'id_externo' => (isset($dados['id_externo']) ? $dados['id_externo'] : null),
			'ip' => $dados['ip']
		]);

		$cliente->save();

		return $cliente;
	}
	
	public function cadastrarUsuario(Clientes $cliente, $senha) {
	
		$user = (new \App\User)->create([
			'name'      => $cliente->nome_cliente,
			'email'     => $cliente->email,
			'active'	=> 0,
			'password'  => Hash::make($senha)
		]);
		
		$cliente->id_usuario = $user->id;
		$cliente->save();

		$user->attachRole((new Role)->where('name', 'CLIENTE')->first());

	}

	public function criarUsuarioSuperlogica($dados)
    {

        $nome = $dados["nome"] . (isset($dados['sobrenome']) ? ' ' . $dados['sobrenome'] : '');

        $postData = [
            'ST_TELEFONE_SAC' => $dados['celular'],
            'ST_NOME_SAC' => $nome,
            'ST_NOMEREF_SAC' => $nome,
            'ST_CGC_SAC' => $dados['cpf'],
            'ST_EMAIL_SAC' => $dados["email"],
            'ST_DIAVENCIMENTO_SAC' => Carbon::now()->day,

            'ID_GRUPO_GRP' => 1,

            'ST_CEP_SAC' => $dados['cep'],
            'ST_ENDERECO_SAC' => $dados['rua'],
            'ST_NUMERO_SAC' => $dados['numero_endereco'],
            'ST_BAIRRO_SAC' => $dados['bairro'],
            'ST_COMPLEMENTO_SAC' => isset($dados['complemento_endereco']) ? $dados['complemento_endereco'] : '',
            'ST_CIDADE_SAC' => $dados['cidade'],
            'ST_ESTADO_SAC' => $dados['estado'],
        ];

		$infoPagamento = [
			'FL_PAGAMENTOPREF_SAC' => 0
		];
        
        $postData = array_merge($postData, $infoPagamento);
        $response = (new Client())->register($postData);

        return $response;
	}
	
	protected function cadastrarPets($dados) {

        foreach ($dados['pets'] as $pet) {

            // Trata formatos
            $mesReajuste = Carbon::now()->month;
            $mesReajuste = abs($mesReajuste);
		
            $novoPet = (new Pets)->create([
                'nome_pet' => $pet["nome"],
                'tipo' => $pet["tipo"],
                'sexo' => $pet["sexo"],
                'id_raca' => $pet["id_raca"],
                'data_nascimento' => $pet['data_nascimento'],
                'contem_doenca_pre_existente' => 0,
                'doencas_pre_existentes' => '',
                'observacoes' => '',
                'id_cliente' => $dados['cliente']->id,
                'ativo' => 0,
                'familiar' => 0,
                'participativo' => 0,
                'regime' => $pet['regime'],
                'mes_reajuste' => $mesReajuste,
                'valor' => $pet['preco'],
                'numero_microchip' => '0',
                'exame_ultimos_12_meses' => isset($pet['exame_ultimos_12_meses']) ? $pet['exame_ultimos_12_meses'] : 0
            
            ]);

        }
	}

	public function enviarEmailClienteAprovadoAtendimento($dados) 
	{
		
		$mail = Mail::send('mail.lifepet_plus.cliente_aprovado_atendimento', 
		['nome' => $dados->nome_cliente,
		'data_adesao' => $dados->created_at->format('d/m/Y H:i:s'),
		'link_cliente' => "http://app.lifepet.com.br/clientes/".$dados->id."/edit"
		], function($message) {
			$message->to('atendimento@lifepet.com.br');
			$message->to('thiago@vixgrupo.com.br');
			$message->to('alexandre.moreira@lifepet.com.br');
			$message->bcc('breno.grillo@vixsolution.com');
				
			$message->subject('LIFEPET+: Novo cliente aprovado');
			$message->from('noreply@lifepet.com.br', 'Lifepet');
		});
	}

	public function enviarEmailErroSuperlogica($dados) 
	{

		if(isset($dados['cartao_nome'])) {
			unset($dados['cartao_nome']);
		}

		if(isset($dados['cartao_bandeira'])) {
			unset($dados['cartao_bandeira']);
		}

		if(isset($dados['cartao_cod_seguranca'])) {
			unset($dados['cartao_cod_seguranca']);
		}

		if(isset($dados['cartao_ano_validade'])) {
			unset($dados['cartao_ano_validade']);
		}

		if(isset($dados['cartao_mes_validade'])) {
			unset($dados['cartao_mes_validade']);
		}

		if(isset($dados['cartao_numero'])) {
			unset($dados['cartao_numero']);
		}
	
		
		$mail = Mail::send('mail.lifepet_plus.erro_superlogica', ['dados' => $dados], function($message) {
			$message->to('breno.grillo@vixsolution.com');
			$message->to('atendimento@lifepet.com.br');
			$message->subject('ADESÃO REEMBOLSO');
			$message->from('noreply@lifepet.com.br', 'Lifepet');
		});
	}

	
	public function enviarEmailBoasVindas(Clientes $cliente)
	{


		$mail = Mail::send('mail.lifepet_plus.boas_vindas', array(), function($message) use ($cliente) {
			$message->to($cliente->email);
			$message->subject('Agora o seu pet está seguro e você tranquilo!');
			$message->from('noreply@lifepet.com.br', 'Lifepet');
		});
		
		return true;
	}


}
