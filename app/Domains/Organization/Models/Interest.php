<?php

namespace App\Domains\Organization\Models;

use Database\Factories\InterestFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Interest extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function newFactory()
    {
        return InterestFactory::new();
    }
}
