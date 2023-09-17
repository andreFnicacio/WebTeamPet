<?php

namespace App\Jobs;

use App\Helpers\API\Financeiro\DirectAccess\Services\CustomerService;
use App\Models\Clientes;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncClientInfoWithFinance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $clients;

    /**
     * Create a new job instance.
     *
     * @param Clientes[] $clients
     * @return void
     */
    public function __construct(array $clients)
    {
        $this->clients = $clients;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $service = new CustomerService();

        foreach ($this->clients as $client) {
            $service->syncNames($client);
        }
    }
}
