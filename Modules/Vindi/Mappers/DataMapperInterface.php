<?php

namespace Modules\Vindi\Mappers;

interface DataMapperInterface
{
    public function toRequest(array $request);
    public function fromResponse(array $response);
}