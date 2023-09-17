<?php

namespace Modules\Vindi\Console;

use App\Helpers\Utils;
use App\Models\PetsPlanos;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Modules\Vindi\Services\VindiService;

class RemoveSubscriptions extends Command
{
    protected $signature = 'vindi:delete-subscriptions {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove invalid subscriptions from financial service.';

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

        $subscriptions = Utils::csvToArray($filePath);
        $subscriptionService = $this->financialService->subscription();

        foreach ($subscriptions as $subscription) {
            $subscriptionFinancialId = $subscription['subscription_id'];
            $subscriptionModel = PetsPlanos::where('financial_id', $subscriptionFinancialId)->first();

            if (is_null($subscriptionModel)) {
                $this->warn("Subscription " . $subscriptionFinancialId . " not found on ERP, skipping...");
                continue;
            }

            try {
                $subscriptionResponse = $subscriptionService->get($subscriptionFinancialId);
                sleep(1);
                $this->info("Subscription " . $subscriptionFinancialId . " found on financial service");
                try {
                    $subscriptionUpdateResponse = $subscriptionService->put($subscriptionResponse->id, [
                        'code' => null
                    ]);

                    $this->info(
                        "Subscription " . $subscriptionFinancialId . " updated successfully"
                    );

                    if (is_null($subscriptionUpdateResponse->code)) {
                        $subscriptionModel->financial_id = null;
                        $subscriptionModel->save();

                        $this->info(
                            "Subscription " . $subscriptionModel->id . " set financial_id to null successfully"
                        );
                    }

                    try {
                        $this->info(
                            "Deleting subscription " . $subscriptionFinancialId . " from financial service"
                        );
                        $subscriptionService->delete($subscriptionFinancialId);
                        $this->info("Subscription " . $subscriptionFinancialId . " successfully removed");
                        sleep(1);
                    } catch (\Exception $e) {
                        $this->warn(
                            sprintf(
                                "Unable to remove subscription %s from financial service: %s",
                                $subscriptionFinancialId,
                                $e->getMessage()
                            )
                        );
                    }

                } catch (\Exception $e) {
                    $this->warn("Unable to update code on subscription " . $subscriptionFinancialId . ": " . $e->getMessage());
                }
            } catch (\Exception $e) {
                $this->error("Unable to process subscription delete: " . $e->getMessage());
            }
        }
    }
}