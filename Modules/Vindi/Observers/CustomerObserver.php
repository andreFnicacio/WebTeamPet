<?php

namespace Modules\Vindi\Observers;

use App\Models\Clientes;
use Modules\Vindi\Jobs\CreateFinancialCustomer;
use Modules\Vindi\Jobs\UpdateFinancialCustomer;

class CustomerObserver
{
    /**
     * Handle the clientes "created" event.
     *
     * @param Clientes $customer
     * @return void
     */
    public function created(Clientes $customer)
    {
        CreateFinancialCustomer::dispatch($customer);
    }

    public function updated(Clientes $customer)
    {
        UpdateFinancialCustomer::dispatch($customer);
    }
}