<?php

namespace App\Jobs;

use App\Helpers\API\Superlogica\V2\Customer;
use App\Models\Clientes;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SyncSuperlogicaClientInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $cliente;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Clientes $cliente)
    {
        $this->cliente = $cliente;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws Exception
     */
    public function handle($verbose = false)
    {
        $customerService = new Customer();

        if(!$this->cliente->dia_vencimento) {
            if($verbose) {
                echo "Cliente não possui data de vencimento definida.\n";
            }
            return;
        }

        if(!$this->cliente->ativo) {
            echo "Cliente inativo. Enviando solicitação de inatividade para ele.\n";
            $customerService->inactivate($this->cliente);
            return;
        }

        if(!$this->cliente->id_superlogica && !$this->cliente->new_superlogica_id) {
            echo "Cliente não possui ID do Superlógica. Criando.\n";
            $customerService->createFromCustomerData($this->cliente);
            return;
        }

        echo "Atualizando dados de cliente\n";
        $customerService->update($this->cliente);
        $this->cliente->last_sync = now();
        $this->cliente->update();
    }
}
