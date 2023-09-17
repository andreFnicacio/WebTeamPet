<?php

namespace Modules\Vindi\Mappers;

class AddressDataMapper extends AbstractDataMapper implements DataMapperInterface
{
    protected $imutables = [
        "country" => "BR"
    ];

    protected $mapper = [
        "rua" => "street",
        "numero_endereco" => "number",
        "complemento_endereco" => "additional_details",
        "cep" => "zipcode",
        "bairro" => "neighborhood",
        "cidade" => "city",
        "estado" => "state"
    ];
}