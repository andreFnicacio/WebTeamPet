<?php

namespace App\Jobs;

use App\Helpers\API\Superlogica\V2\Signature;
use App\Models\PetsPlanos;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SuperlogicaUpdateSignatureInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contrato;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(PetsPlanos $contrato)
    {
        $this->contrato = $contrato;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(!$this->contrato->id_contrato_superlogica) {
            return;
        }

        $service = new Signature();
        $service->sync($this->contrato);
    }
}
