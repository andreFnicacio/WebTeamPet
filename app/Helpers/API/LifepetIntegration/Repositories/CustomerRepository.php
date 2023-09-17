<?php

namespace App\Helpers\API\LifepetIntegration\Repositories;
use App\Helpers\API\Financeiro\Financeiro;
use App\Helpers\API\LifepetIntegration\Domains\Customer\Customer;
use App\Http\Util\Logger;
use App\Models\Clientes;
use Carbon\Carbon;

class CustomerRepository {

    public function save(Customer $customer)  : int {

		if($customer->getId() == null) {
			$clientes = new Clientes();
		} else {
			$clientes = Clientes::find($customer->getId());
		}

        $address = $customer->getAddress();
		$birthdate = \DateTime::createFromFormat('Y-m-d', $customer->getBirthdate());

		if($customer->getName() != null) {
			$clientes->nome_cliente = $customer->getName();
		}

		if($customer->getCPF() != null) {
			$clientes->cpf = $customer->getCPF();
		}

		if($birthdate != null) {
			$clientes->data_nascimento = $birthdate->format('d/m/Y');
		}

		if($customer->getPhone() != null) {
			$clientes->celular = $customer->getPhone();
		}

		if($customer->getEmail() != null) {
			$clientes->email = $customer->getEmail();
		}

		if($customer->getObs() != null) {
			$clientes->observacoes = $customer->getObs();
		}

		if($customer->getActive() !== null) {
			$clientes->ativo = $customer->getActive() === true ? 1 : 0;
		}

		if($customer->getParticipative() !== null) {
			$clientes->participativo = $customer->getParticipative() === true ? 1 : 0;
		}

		if($customer->getExternalId() != null) {
			$clientes->id_externo = $customer->getExternalId();
		}

		if($customer->getIp() != null) {
			$clientes->ip = $customer->getIp();
		}

		if($customer->getPaymentDueDay() != null) {
			$clientes->vencimento = $customer->getPaymentDueDay();
			$clientes->dia_vencimento = $customer->getPaymentDueDay();
		}

		if($customer->getApproved() !== null) {
			$clientes->aprovado = $customer->getApproved() === true ? 1 : 0;
		}

		if($address->getPostalCode() != null) {
			$clientes->cep = preg_replace( '/[^0-9]/', '', $address->getPostalCode() );
		}

		if($address->getStreet() != null) {
			$clientes->rua = $address->getStreet();
		}

		if($address->getNumber() != null) {
			$clientes->numero_endereco = $address->getNumber();
		}

		if($address->getComplement() != null) {
			$clientes->complemento_endereco = $address->getComplement();
		}

		if($address->getNeighborhood() != null) {
			$clientes->bairro = $address->getNeighborhood();
		}

		if($address->getCity() != null) {
			$clientes->cidade = $address->getCity();
		}

		if($address->getState() != null) {
			$clientes->estado = $address->getState();
		}

		if($customer->getSex() != null) {
		    $clientes->sexo = $customer->getSex();
        }

		$clientes->save();

        return $clientes->id;
        
	}
	
	public function getBy($field, $value) {
		$customer = Clientes::where($field, $value);
		if($customer->count() == 0) {
			return [];
		}
		return $this->adapt($customer->first());
	}

    public function getById(int $id) {
		$customer = Clientes::find($id);

		if(!$customer) {
			throw new \Exception('Cliente não encontrado');
		}

		return $this->adapt($customer);
	}

	public function adapt(Clientes $customer) {
		$customerObj = new Customer();

        $birthDate = $customer->data_nascimento;

        if ($birthDate->year === -1) {
            $birthDate = Carbon::now();
        }

		$customerObj->populate([
			'id' => $customer->id,
			'name' => $customer->nome_cliente,
			'cpf' => $customer->cpf,
			'rg' => $customer->rg ?? null,
			'birthdate' => $birthDate->format('Y-m-d'),
			'contract_number' => $customer->numero_contrato ?? null,
			'landline' => !empty($customer->telefone_fixo) ? preg_replace( '/[^0-9]/', '', $customer->telefone_fixo) :  null,
			'phone' => preg_replace( '/[^0-9]/', '', $customer->celular),
			'email' => $customer->email ?? null,
			'active' => $customer->ativo,
			'external_id' => $customer->id_externo ?? null,
			'sex' => $customer->sexo,
			'marital_status' => $customer->marital_status,
			'obs' => $customer->obs ?? null,
			'payment_value' => $customer->valor ?? null,
			'payment_due_day' => $customer->vencimento ?? null,
			'participative' => $customer->participativo ?? null,
			'agreed_id' => $customer->id_conveniado ?? null,
			'user_id' => $customer->id_usuario ?? null,
			'first_access' => $customer->primeiro_acesso,
			'second_responsible_name' => $customer->segundo_responsavel_nome ?? null,
			'second_responsible_email' => $customer->segundo_responsavel_email ?? null,
			'second_responsible_phone' => $customer->segundo_responsavel_telefone ?? null,
			'group' => $customer->grupo ?? null,
			'approved' => $customer->aprovado,
			'signature' => $customer->assinatura ?? null,
			'token_firebase' => $customer->token_firebase ?? null,
			'proposal_data' => $customer->dados_proposta ?? null,
			'plan_password' => $customer->senha_plano,
			'token' => $customer->token ?? null,
			'ip' => $customer->ip ?? null
		]);

		$customerObj->address->populate([
			'postal_code' => $customer->cep,
			'street' => $customer->rua,
			'number' => $customer->numero_endereco,
			'complement' => $customer->complemento_endereco ?? null,
            'neighborhood' => $customer->bairro,
            'city' => $customer->cidade,
            'state' => $customer->estado
		]);
		
		return $customerObj;
	}


	public function addNote(int $customerId, string $text) {
		$customer = $this->model()::find($customerId);

		if(!$customer) {
			Log::error("Falha ao adicionar a nota: cliente {$customerId} não existe.");
			return false;
		}

		$customer->notas()->create([
			'corpo' => $text,
			'user_id' => 1
		]);

		return true;
	}
	
	private function model() {
        return \App\Models\Clientes::class;
    }

    public function linkWithFinance($id)
    {
        $cliente = Clientes::find($id);
        $logger = new Logger('clientes', 'clientes');
        /**
         * @var Clientes $cliente
         */
        if(!$cliente) {
            $logger->register(\App\Http\Util\LogEvent::WARNING, \App\Http\Util\LogPriority::MEDIUM,
                "Não foi possível vincular o cliente com o Sistema Financeiro. Motivo: Não foi possível encontrar o cliente de ID #{$id}"
            );
            return false;
        }

        $financeiro = new Financeiro();
        $customer = $financeiro->customer($cliente->cpf);
        if(!$customer) {
            return $cliente->syncWithFinance();
        }

        $cliente->id_externo = $customer->ref_code;
        $cliente->update();
        return true;
    }

    public function setPaymentType($id, $paymentType)
    {
        $cliente = Clientes::find($id);
        if(!$cliente) {
            return false;
        }

        $cliente->forma_pagamento = $paymentType;
        $cliente->update();

        return true;
    }
}