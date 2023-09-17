<?php

use Faker\Factory as Faker;
use App\Models\Clientes;
use App\Repositories\ClientesRepository;

trait MakeClientesTrait
{
    /**
     * Create fake instance of Clientes and save it in database
     *
     * @param array $clientesFields
     * @return Clientes
     */
    public function makeClientes($clientesFields = [])
    {
        /** @var ClientesRepository $clientesRepo */
        $clientesRepo = App::make(ClientesRepository::class);
        $theme = $this->fakeClientesData($clientesFields);
        return $clientesRepo->create($theme);
    }

    /**
     * Get fake instance of Clientes
     *
     * @param array $clientesFields
     * @return Clientes
     */
    public function fakeClientes($clientesFields = [])
    {
        return new Clientes($this->fakeClientesData($clientesFields));
    }

    /**
     * Get fake data of Clientes
     *
     * @param array $postFields
     * @return array
     */
    public function fakeClientesData($clientesFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'nome_cliente' => $fake->word,
            'cpf' => $fake->word,
            'rg' => $fake->word,
            'data_nascimento' => $fake->word,
            'numero_contrato' => $fake->randomDigitNotNull,
            'cep' => $fake->word,
            'rua' => $fake->word,
            'numero_endereco' => $fake->word,
            'complemento_endereco' => $fake->word,
            'bairro' => $fake->word,
            'cidade' => $fake->word,
            'estado' => $fake->word,
            'telefone_fixo' => $fake->word,
            'celular' => $fake->word,
            'email' => $fake->word,
            'ativo' => $fake->word,
            'id_externo' => $fake->randomDigitNotNull,
            'sexo' => $fake->word,
            'estado_civil' => $fake->word,
            'observacoes' => $fake->text,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'deleted_at' => $fake->date('Y-m-d H:i:s')
        ], $clientesFields);
    }
}
