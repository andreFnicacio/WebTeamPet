<?php

namespace Modules\Vindi\Console;

use App\Helpers\Utils;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Modules\Vindi\Services\VindiService;

class RemoveInvalidCharges extends Command
{
    protected $signature = 'vindi:delete-charges {file}';

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
        $filePath = $this->argument('file');

        if (!file_exists(base_path($filePath))) {
            throw new FileNotFoundException("File not found");
        }

        $charges = Utils::csvToArray($filePath, ';');

        $chargeService = $this->financialService->charges();
        $billService = $this->financialService->bills();

        foreach ($charges as $charge) {

            try {
                $this->info("Checking if bill exist for a cancelled charge");
                try {
                    $billId = $charge['charge_id'];
                    $billService->delete($billId);

                    $this->info("Bill " . $billId . " removed successfully");
                } catch (\Exception $e) {
                    $this->info("Bill " . $billId . " not removed: " . $e->getMessage());
                }
                sleep(1);

            } catch (\Exception $e) {
                $this->info(
                    "Charge with id " . $charge['charge_id'] . " not found on financial service: " .
                    $e->getMessage()
                );
                sleep(1);
                continue;
            }

            try {
                $chargeService->delete($charge['charge_id']);
                $this->info("Charge " . $charge['charge_id'] . " removed successfully");
            } catch (\Exception $e) {
                $this->info("Charge " . $charge['charge_id'] . " not removed: " . $e->getMessage());
            }

            sleep(1);

            if (isset($chargeResponse->bill)) {
                try {
                    $billId = $chargeResponse->bill->id;
                    $billService->delete($billId);

                    $this->info("Bill " . $billId . " removed successfully");
                } catch (\Exception $e) {
                    $this->info("Bill " . $billId . " not removed: " . $e->getMessage());
                }
                sleep(1);
            }
        }
    }
}