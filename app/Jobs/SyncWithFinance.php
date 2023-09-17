<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use stdClass;
///Illuminate\Queue\Middleware\WithoutOverlapping
class SyncWithFinance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $tries = 5;

    protected $model;
    /**
     * Create a new job instance.
     * @return void
     */
    public function __construct($model)
    {
        $this->model = $model;
       // $this->onQueue('queueSF');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->model->syncFinance();

    }

    public function middleware()
    {
        return [new ThrottlesExceptions(10, 5)];
    }

    public function retryUntil()
    {
        return now()->addMinutes(5);
    }

}
