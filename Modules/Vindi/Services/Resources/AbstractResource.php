<?php

namespace Modules\Vindi\Services\Resources;

use Modules\Vindi\Helper\Data;
use Vindi\Resource;

abstract class AbstractResource
{
    protected Resource $service;

    public function __construct(Resource $service)
    {
        $this->service = $service;
    }

    public function get(int $id)
    {
        return $this->service->get($id);
    }

    public function getByCode($code)
    {
        $data = $this->service->all(['query' => "code=" . $code]);
        return $this->toArray($data);
    }

    protected function toArray($response)
    {
        return Data::toArray($response);
    }
}