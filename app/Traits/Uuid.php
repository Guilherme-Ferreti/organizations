<?php

namespace App\Traits;

use Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid 
{
    protected static function bootUuid() 
    {
        static::creating(function ($model) {
            $model->uuid = (string) RamseyUuid::uuid4();
        });
    }
}