<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $table = 'emails';
    protected $dates = ['created_at', 'updated_at', 'read_at'];

    protected function emailable() {
        return $this->morphTo();
    }
}
