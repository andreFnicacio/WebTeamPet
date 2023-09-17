<?php
/**
 * Created by PhpStorm.
 * User: desenvolvimento
 * Date: 03/04/19
 * Time: 15:00
 */

namespace Tests\traits;
use App\Models\Clientes;
use App\Models\Pets;
use App\Models\PetsPlanos;
use App\Models\Planos;
use App\Models\Procedimentos;
use App\Models\Raca;
use App\Models\Vendedores;
use Faker\Factory as Faker;
use Modules\Clinics\Entities\Clinicas;
use Modules\Veterinaries\Entities\Prestadores;

trait DataFactory
{
    /**
     * @param int $ativo
     * @return Planos|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRandomPlano($ativo = 1)
    {
        return Planos::orderByRaw("RAND()")->where('ativo', 1)->first();
    }

    /**
     * @return Raca|null
     */
    public function getRandomRaca()
    {
        return Raca::orderByRaw("RAND()")->first();
    }

    /**
     * @return Vendedores|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRandomVendedor()
    {
        return Vendedores::orderByRaw("RAND()")->first();
    }

    /**
     * @return Procedimentos|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRandomProcedimento()
    {
        return Procedimentos::orderByRaw("RAND()")->first();
    }

    /**
     * @return Prestadores
     */
    public function getRandomPrestador()
    {
        return Prestadores::orderByRaw("RAND()")->first();
    }

    /**
     * @return Clinicas|\Illuminate\Database\Eloquent\Model|null
     */
    public function getRandomClinica()
    {
        return Clinicas::orderByRaw("RAND()")->first();
    }

    /**
     * @param int $ativo
     * @return Clientes|\Illuminate\Database\Eloquent\Model
     */
    public function makeClienteData($ativo = 1)
    {
        $fake = Faker::create("pt_BR");

        $created_at = $updated_at = $fake->dateTime;

        $clienteData = [
            'nome_cliente' => $fake->name,
            'cpf' => $fake->cpf,
            'rg' => $fake->rg,
            'data_nascimento' => $fake->dateTime->format('d/m/Y'),
            'numero_contrato' => $fake->randomNumber(4, true),
            'cep' => $fake->postcode,
            'rua' => $fake->streetName,
            'numero_endereco' => $fake->randomNumber(4),
            'complemento_endereco' => $fake->randomElement([
                $fake->word,
                null
            ]),
            'bairro' => $fake->word,
            'cidade' => $fake->city,
            'estado' => $fake->stateAbbr,
            'telefone_fixo' => $fake->phone,
            'celular' => $fake->phoneNumber,
            'email' => $fake->email,
            'ativo' => $ativo,
            'id_externo' => null,
            'sexo' => $fake->randomElement(['GATO', 'CACHORRO']),
            'estado_civil' => $fake->randomElement(['CASADO', 'SOLTEIRO', 'DIVORCIADO']),
            'observacoes' => $fake->text,
            'created_at' => $created_at,
            'updated_at' => $updated_at,
            'deleted_at' => null
        ];

        return Clientes::create($clienteData);
    }


    /**
     * @param int $ativo
     * @param int $participativo
     * @param null $dataInicioContrato
     * @return Pets
     */
    public function makePetData($ativo = 1, $participativo = 0, $dataInicioContrato = null)
    {
        $cliente = $this->makeClienteData(1);

        $fake = Faker::create();

        $pet = new Pets();
        $pet->nome_pet = $fake->name;
        $pet->tipo = $fake->randomElement(['GATO', 'CACHORRO']);
        $pet->id_raca = $this->getRandomRaca()->id;
        $pet->sexo = $fake->randomElement(['M', 'F']);
        $pet->id_externo = null;
        $pet->numero_microchip = $fake->randomNumber(9) . $fake->randomNumber(6);
        $pet->data_nascimento = $fake->dateTime->format('d/m/Y');
        $pet->id_cliente = $cliente->id;
        $pet->contem_doenca_pre_existente = $fake->boolean();
        $pet->doencas_pre_existentes = $fake->text;
        $pet->familiar = $fake->boolean;
        $pet->observacoes = $fake->text;
        $pet->ativo = $ativo;
        $pet->regime = $fake->randomElement(Pets::$regimes);
        $pet->valor = $fake->randomFloat(2, 50, 200);
        $pet->vencimento = $fake->randomElement([5,10,15,20,25]);
        $pet->participativo = $participativo;
        $pet->mes_reajuste = $fake->numberBetween(1,12);
        $pet->save();

        $plano = $this->getRandomPlano();
        $vendedor = $this->getRandomVendedor();

        $pet->adicionarPlano($plano, PetsPlanos::STATUS_PRIMEIRO_PLANO, $fake->boolean, $vendedor, $dataInicioContrato);

        return $pet;
    }

    public function makeHistoricoUsoData()
    {
        $fake = Faker::create('pt_BR');

        $procedimento = $this->getRandomProcedimento();
        $pet = $this->makePetData();
        $prestador = $this->getRandomPrestador();
        $clinica = $this->getRandomClinica();

        $data = [
            'id_pet' => $pet->id,
            'id_procedimento' => $procedimento->id,
            'id_plano' => $pet->planoAtual->id,
            'id_prestador' => $prestador->id,
            'id_prestador_solicitante' => $prestador->id,
            'id_clinica' => $clinica->id,
            'id_especialidade' => $prestador->especialidade->id,
            'numero_guia' => $this->numeroGuia,
            'valor_momento' => $procedimento->valor_momento,
//            'justificativa' => $this->justificativa,
//            'laudo' => $this->laudo,
//            'observacao' => $this->observacao,
//            'autorizacao' => $this->autorizacao,
//            'tipo_atendimento' => $this->tipoAtendimento,
//            'status' => $this->status,
//            'id_autorizador' => $this->id_autorizador,
//            'id_solicitador' => $this->solicitador->id,
//            'created_at' => $this->created_at
        ];
    }
}