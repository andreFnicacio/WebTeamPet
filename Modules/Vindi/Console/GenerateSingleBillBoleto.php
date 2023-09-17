<?php

namespace Modules\Vindi\Console;

use App\Helpers\Utils;
use App\Models\Clientes;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Modules\Vindi\Services\VindiService;
use PHPUnit\Exception;

class GenerateSingleBillBoleto extends Command
{
    protected $signature = 'vindi:generate-single-bill-boleto {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a single bill on financial service to capture customers boleto info';

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

        $bills =Utils::csvToArray($filePath, ';');
        $billService = $this->financialService->bills();

        foreach ($bills as $bill) {
            $this->info('Gerando fatura para o cliente :'.$bill['customer_id']);
            $data = [];
            $data['customer_id'] = (int)$bill['customer_id'];
            $data['payment_method_code'] = $bill['payment'];
            $data['due_at'] = $bill['vencimento'];
            $billing_date = date('d/m/Y', strtotime("+1 day"));
            if(date($data['due_at']) > $billing_date) {
                $data['billing_at'] = (string)$billing_date;
            }
            $data['bill_items'][0]['product_id'] = (int)$bill['code_product'];
            $data['bill_items'][0]['amount'] = (float)str_replace(',', '.',$bill['value']);

            try {
                $billService->createBills($data);
            } catch (Exception $e){
                $this->error('Error '.$e);
            }
            sleep(1);
        }
    }
}