<?php

namespace App\Domains\Organization\Models;

use Illuminate\Database\Eloquent\Model;
use Database\Factories\OrganizationTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function newFactory()
    {
        return OrganizationTypeFactory::new();
    }
}
