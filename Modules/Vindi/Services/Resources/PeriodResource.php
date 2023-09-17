<?php

namespace Modules\Vindi\Services\Resources;

use Vindi\Period;

class PeriodResource extends AbstractResource
{
    public function __construct(Period $service)
    {
        parent::__construct($service);
    }
}