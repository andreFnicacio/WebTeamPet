<?php

namespace Modules\Vindi\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Modules\Vindi\Services\VindiService;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CleanupSandbox extends Command
{
    const REQUESTS_PER_MINUTE = 120;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'vindi:clean-up-sandbox';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform a mass delete of customers and associated subscriptions in order to reset test environment';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (!$this->isDevelopmentMode()) {
            $this->error("Do not run this command on production environment!");
            return;
        }

        $customerFinancialService = app(VindiService::class)->customer();
        $customers = $customerFinancialService->findAll("status:inactive");

        $i = 1;
        if (count($customers) > 0) {
            foreach ($customers as $customer) {

                // Wait per API limitation of 120 per minute
                if ($i === self::REQUESTS_PER_MINUTE) {
                    $this->info("Max of " . $i . "requests per minute reached. Waiting 60 seconds...");
                    sleep(65);
                    $i = 1;
                }

                $this->deleteCustomer($customerFinancialService, $customer->id);
                $i++;
            }

            $this->handle();
        }

        $this->info("No customers to be deleted.");
    }

    private function deleteCustomer($customerFinancialService, $customerId)
    {
        try {
            $customerFinancialService->delete($customerId);
        } catch (\Exception $e) {
            $this->error("Unable to delete customer " . $customerId . " | Error: " . $e->getMessage());

            $this->info("\n Waiting 60 seconds to try again...");
            sleep(60);
            $this->info("\n Continuing...");
        }
    }

    private function isDevelopmentMode(): bool
    {
        return App::environment() === "local";
    }
}
