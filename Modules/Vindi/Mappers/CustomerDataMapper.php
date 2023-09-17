<?php

namespace Modules\Vindi\Mappers;

class CustomerDataMapper extends AbstractDataMapper implements DataMapperInterface
{
    protected $mapper = [
        "nome_cliente" => "name",
        "email" => "email",
        "cpf" => "registry_code",
        "id" => "code"
    ];

    protected $unsetAfterParseToRequest = [
        "id"
    ];
}