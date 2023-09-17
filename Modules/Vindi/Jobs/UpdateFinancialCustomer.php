<?php

namespace Modules\Vindi\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Vindi\DTO\Customer\CustomerDTO;
use Modules\Vindi\Services\Resources\CustomerResource;
use Modules\Vindi\Services\VindiService;
use Spatie\DataTransferObject\DataTransferObjectError;

class UpdateFinancialCustomer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $customer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(VindiService $service)
    {
        /** @var VindiService $service */
        $customerService = $service->customer();

        try {
            $customerData = $customerService->map($this->customer->toArray());
            $dto = new CustomerDTO($customerData);
        } catch (\Throwable $exception) {
            throw new DataTransferObjectError($exception->getMessage());
        }

        $customerService->updateCustomer($dto);
    }
}
