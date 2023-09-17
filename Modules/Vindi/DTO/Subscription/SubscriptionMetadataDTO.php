<?php

namespace Modules\Vindi\DTO\Subscription;

use Spatie\DataTransferObject\FlexibleDataTransferObject;

class SubscriptionMetadataDTO extends FlexibleDataTransferObject
{
    public ?string $pet_id;
    public ?string $pet_name;
}