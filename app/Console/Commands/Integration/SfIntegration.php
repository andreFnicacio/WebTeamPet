<?php

namespace App\Console\Commands\Integration;

use App\Jobs\SyncWithFinance;
use Illuminate\Console\Command;
use App\Models\Integration\SfIntegration as SfIntegrationModel;

class SfIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'integration:sf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Integrar clientes SF';

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
        /**
         * sincronizar todos os sync
         */
        $sfIntegrations = SfIntegrationModel::orderBy('last_sync_at')->limit(400)->get();

        foreach($sfIntegrations as $sfIntegration)
        {
            if (empty($sfIntegration->cliente)) {
                continue;
            }

            $this->info("Syncing client " . $sfIntegration->cliente->id . " with SF");
            SyncWithFinance::dispatch($sfIntegration->cliente);
        }
    }
}
