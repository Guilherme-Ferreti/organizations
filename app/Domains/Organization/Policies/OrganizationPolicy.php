<?php

namespace App\Domains\Organization\Policies;

use App\Domains\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Organization $organization)
    {
        return $organization->isActiveOwner($user);
    }
}
