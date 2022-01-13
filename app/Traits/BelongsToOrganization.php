<?php

namespace App\Traits;

use App\Domains\Organization\Models\Organization;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization()
    {
        static::creating(function ($model) {
            if (request()->organization) {
                $model->organization_id = request()->organization->id;
            }
        });
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}