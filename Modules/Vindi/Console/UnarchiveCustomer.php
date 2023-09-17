<?php

namespace Modules\Vindi\Console;

use App\Helpers\Utils;
use App\Models\Clientes;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Modules\Vindi\Services\VindiService;
use PHPUnit\Exception;

class UnarchiveCustomer extends Command
{
    protected $signature = 'vindi:unarchive-customer {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inactive Customer Vindi';

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
        $filePath = $this->argument('file');

        if (!file_exists(base_path($filePath))) {
            throw new FileNotFoundException("File not found");
        }

        $idCustomers =Utils::csvToArray($filePath, ';');
        $customerService = $this->financialService->customer();

        foreach ($idCustomers as $customer) {
            $this->info('Inativando o cliente :'.$customer['id']);
            try {
                $customerVindi = $customerService->get($customer['id']);
                if($customerVindi && $customerVindi->status == 'active') {
                    $customerService->delete($customer['id']);
                }
            } catch (Exception $e){
                $this->error('Error '.$e);
                continue;
            }
            sleep(1);
        }
        $this->info('Finalizado a inativação');
    }
}