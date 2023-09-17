<?php

namespace App\Providers;

use App\Models\Clientes;
use App\Models\PetsPlanos;
use App\Observers\ClientesObserver;
use App\Observers\PetsPlanosObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Vindi\Observers\CustomerObserver;
use Modules\Vindi\Observers\SubscriptionObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        Clientes::observe([
            ClientesObserver::class,
            CustomerObserver::class
        ]);
        PetsPlanos::observe([
            PetsPlanosObserver::class,
            SubscriptionObserver::class
        ]);
        //
    }
}