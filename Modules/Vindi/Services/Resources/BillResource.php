<?php

namespace Modules\Vindi\Services\Resources;

use GuzzleHttp\Exception\GuzzleException;
use Vindi\Bill;
use Vindi\Exceptions\RateLimitException;
use Vindi\Exceptions\RequestException;

class BillResource extends AbstractResource
{
    public function __construct(Bill $service)
    {
        parent::__construct($service);
    }

    /**
     * @throws GuzzleException
     * @throws RateLimitException
     * @throws RequestException
     */
    public function delete($billId)
    {
        $this->service->delete($billId);
    }

    public function createBills(array $request){
        $response = $this->service->create($request);
        return $this->toArray($response);
    }
}