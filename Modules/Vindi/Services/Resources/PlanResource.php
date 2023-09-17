<?php

namespace Modules\Vindi\Services\Resources;

use Modules\Vindi\DTO\Plan\PlanDTO;
use Vindi\Resource;

class PlanResource extends AbstractResource
{
    public function __construct(Resource $service)
    {
        parent::__construct($service);
    }

    public function getByCode($code)
    {
        $data = parent::getByCode($code);

        if (!is_array($data) && !is_array(current($data))) {
            throw new \Exception("Plan not found");
        }

        return new PlanDTO(current($data));
    }

    public function get(int $id)
    {
        $response = $this->service->get($id);
        return new PlanDTO($this->toArray($response));
    }
}