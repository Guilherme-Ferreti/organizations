<?php

namespace App\Domains\Organization\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Database\Factories\Domains\Organization\OrganizationTypeFactory;

class OrganizationType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected static function newFactory()
    {
        return OrganizationTypeFactory::new();
    }
}
