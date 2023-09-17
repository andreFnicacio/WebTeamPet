<?php

namespace Modules\Vindi\Console;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Modules\Vindi\Services\VindiService;
use App\Models\Clientes;
use App\Models\PetsPlanos;
use http\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Vindi\DTO\Customer\CustomerAddressDTO;
use Modules\Vindi\DTO\Customer\CustomerDTO;
use Modules\Vindi\DTO\Customer\CustomerPhoneDTO;
use Modules\Vindi\DTO\Customer\CustomerPhoneDTOCollection;
use Modules\Vindi\Services\Resources\CustomerResource;
use Vindi\Customer;

class UpdateRegisterCode extends Command
{
    protected $signature = 'vindi:update-register-code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove invalid charges from financial service.';

    /**
     * @var VindiService
     */
    private $financialService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(VindiService $financialService)
    {
        parent::__construct();
        $this->financialService = $financialService;
    }

    public function handle()
    {
        $this->info('Iniciando migração.');
        $clients = $this->getClients();
        $this->updateRegisterCodeVindi($clients);
    }

    private function getClients(){
        $query = \DB::select ('SELECT * FROM clientes WHERE financial_id IS NOT NULL ');

        return $query;
    }

    private function updateRegisterCodeVindi($clients){
        $customerService = app(VindiService::class)->customer();
        $data = null;
        foreach ($clients as $client){
            $data['registry_code'] = $this->formater_cpf_cnpj($client->cpf);
            try {
                $customerService->put($client->financial_id, $data);
                $this->info('Client:'.$client->financial_id.' sync register_code');
            } catch (\Exception $e){
                $this->warn('Client:'.$client->financial_id.' error sync register_code');
                continue;
            }
            sleep(1);
        }

    }

    function formater_cpf_cnpj($doc) {

        $doc = preg_replace("/[^0-9]/", "", $doc);
        $qtd = strlen($doc);

        if($qtd >= 11) {

            if($qtd === 11 ) {

                $docFormatado = substr($doc, 0, 3) . '.' .
                    substr($doc, 3, 3) . '.' .
                    substr($doc, 6, 3) . '.' .
                    substr($doc, 9, 2);
            } else {
                $docFormatado = substr($doc, 0, 2) . '.' .
                    substr($doc, 2, 3) . '.' .
                    substr($doc, 5, 3) . '/' .
                    substr($doc, 8, 4) . '-' .
                    substr($doc, -2);
            }

            return $docFormatado;

        } else {
            return 'Documento invalido';
        }
    }
}