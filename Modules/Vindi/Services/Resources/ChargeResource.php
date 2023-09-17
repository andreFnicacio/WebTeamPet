<?php

namespace Modules\Vindi\Services\Resources;

use Vindi\Charge;
use Vindi\Exceptions\RateLimitException;
use Vindi\Exceptions\RequestException;

class ChargeResource extends AbstractResource
{
    public function __construct(Charge $service)
    {
        parent::__construct($service);
    }

    /**
     * @throws GuzzleException
     * @throws RateLimitException
     * @throws RequestException
     */
    public function delete($chargeId)
    {
        $this->service->delete($chargeId);
    }
}