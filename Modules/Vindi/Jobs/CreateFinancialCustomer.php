<?php

namespace Modules\Vindi\Jobs;

use App\Models\Clientes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Vindi\Services\Resources\CustomerResource;
use Modules\Vindi\Services\VindiService;
use Spatie\DataTransferObject\DataTransferObjectError;

class CreateFinancialCustomer implements ShouldQueue
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
     * @throws \Exception
     */
    public function handle()
    {
        /** @var CustomerResource $service */
        $service = app(VindiService::class)->customer();

        try {
            $customerData = $service->map($this->customer->toArray());
        } catch (\Throwable $exception) {
            throw new DataTransferObjectError($exception->getMessage());
        }

        try {
            $financialCustomer = $service->findOrCreate($customerData);
            $this->customer->financial_id = $financialCustomer->id;

            // Disable event listener during creation to avoid customer update on financial service
            Clientes::unsetEventDispatcher();
            $this->customer->save();
        } catch (\Exception $e) {
            Log::error("Unable to create customer on financial service: " . $e->getMessage());
            throw new \Exception("Não foi possível criar o usuário na Vindi, tente a sincronização manual");
        }

    }
}
