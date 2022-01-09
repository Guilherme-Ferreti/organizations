<?php

namespace App\Domains\Organization\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Domains\Organization\Models\Organization;

class OrganizationPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Organization $organization)
    {
        return $organization->isActiveOwner($user);
    }
    
    public function delete(User $user, Organization $organization)
    {
        return $organization->isActiveOwner($user);
    }

    public function addMembers(User $user, Organization $organization)
    {
        return $organization->isActiveOwner($user);
    }

    public function removeMembers(User $user, Organization $organization, User $member)
    {
        return $organization->isActiveOwner($user) && ! $organization->isActiveOwner($member);
    }

    public function transferOwnership(User $user, Organization $organization)
    {
        return $organization->isActiveOwner($user);
    }
}
