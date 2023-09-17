<?php

namespace Modules\Vindi\Helper;

class Data
{
    public static function toArray($response)
    {
        return json_decode(json_encode($response), true);
    }
}