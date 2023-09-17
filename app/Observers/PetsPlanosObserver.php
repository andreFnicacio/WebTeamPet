<?php

namespace App\Observers;

use App\Jobs\SuperlogicaSyncSignature;
use App\Jobs\SuperlogicaUpdateSignatureInfo;
use App\Jobs\SyncWithFinance;
use App\Models\PetsPlanos;

class PetsPlanosObserver
{
    /**
     * Handle the pets planos "created" event.
     *
     * @param  \App\Models\PetsPlanos  $petsPlanos
     * @return void
     */
    public function created(PetsPlanos $petsPlanos)
    {
        SyncWithFinance::dispatch( $petsPlanos->pet->cliente );
    }

    /**
     * Handle the pets planos "updated" event.
     *
     * @param  \App\Models\PetsPlanos  $petsPlanos
     * @return void
     */
    public function updated(PetsPlanos $petsPlanos)
    {
        SyncWithFinance::dispatch( $petsPlanos->pet->cliente );
    }

    /**
     * Handle the pets planos "deleted" event.
     *
     * @param  \App\Models\PetsPlanos  $petsPlanos
     * @return void
     */
    public function deleted(PetsPlanos $petsPlanos)
    {
        //
    }

    /**
     * Handle the pets planos "restored" event.
     *
     * @param  \App\Models\PetsPlanos  $petsPlanos
     * @return void
     */
    public function restored(PetsPlanos $petsPlanos)
    {
        //
    }

    /**
     * Handle the pets planos "force deleted" event.
     *
     * @param  \App\Models\PetsPlanos  $petsPlanos
     * @return void
     */
    public function forceDeleted(PetsPlanos $petsPlanos)
    {
        //
    }
}
