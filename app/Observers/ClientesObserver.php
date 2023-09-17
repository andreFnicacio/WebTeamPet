<?php

namespace App\Observers;

use App\Jobs\SyncSuperlogicaClientInfo;
use App\Jobs\SyncWithFinance;
use App\Models\Clientes;

class ClientesObserver
{
    /**
     * Handle the clientes "created" event.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return void
     */
    public function created(Clientes $clientes)
    {
        SyncWithFinance::dispatch($clientes);
    }

    /**
     * Handle the clientes "updated" event.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return void
     */
    public function updated(Clientes $clientes)
    {
        SyncWithFinance::dispatch($clientes);
    }

    /**
     * Handle the clientes "updated" event.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return void
     */
    public function saved(Clientes $clientes)
    {
        SyncWithFinance::dispatch($clientes);
    }


    /**
     * Handle the clientes "deleted" event.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return void
     */
    public function deleted(Clientes $clientes)
    {
        //
    }

    /**
     * Handle the clientes "restored" event.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return void
     */
    public function restored(Clientes $clientes)
    {
        //
    }

    /**
     * Handle the clientes "force deleted" event.
     *
     * @param  \App\Models\Clientes  $clientes
     * @return void
     */
    public function forceDeleted(Clientes $clientes)
    {
        //
    }
}
