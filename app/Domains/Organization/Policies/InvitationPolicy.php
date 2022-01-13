<?php

namespace App\Domains\Organization\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Domains\Organization\Models\Organization;

class InvitationPolicy
{
    use HandlesAuthorization;

    public function create(User $user, Organization $organization)
    {
        return $organization->isActiveOwner($user);
    }
}
