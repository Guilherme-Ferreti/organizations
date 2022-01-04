<?php

namespace App\Domains\Organization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\Domains\Organization\InterestFactory;

class Interest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function newFactory()
    {
        return InterestFactory::new();
    }
}
