<?php

namespace App\Domains\Organization\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class OrganizationUser extends Pivot
{
    protected $casts = [
        'organization_id'       => 'integer',
        'user_id'               => 'integer',
        'is_technical_manager'  => 'boolean',
        'is_owner'              => 'boolean',
        'is_active'             => 'boolean',
    ];
}
