<?php

namespace App\Domains\Organization\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Rules\NonOrganizationMemberRule;
use App\Domains\Organization\Rules\ActiveOrganizationMemberRule;

class OrganizationMemberController extends Controller
{
    public function store(Request $request, Organization $organization)
    {
        $this->authorize('addMembers', $organization);

        $attributes = $request->validate([
            'user_id' => [
                'bail', 'required', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at'),
                new NonOrganizationMemberRule($organization)
            ],
            'is_technical_manager' => ['boolean'],
            'is_owner' => ['boolean'],
        ]);

        $organization->addMember($attributes['user_id'], $attributes);

        return $this->respondCreated();
    }

    public function update(Request $request, Organization $organization, User $member)
    {
        $this->authorize('updateMembers', $organization);

        $attributes = $request->validate([
            'is_technical_manager'  => ['boolean'],
            'is_owner'              => ['boolean'],
            'is_active'             => ['boolean'],
        ]);

        $organization->updateMember($member, $attributes);

        return $this->respondOk();
    }

    public function destroy(Organization $organization, User $member)
    {
        $this->authorize('removeMembers', [$organization, $member]);

        $organization->removeMember($member);

        return $this->respondNoContent();
    }

    public function transferOwnership(Request $request, Organization $organization)
    {
        $this->authorize('transferOwnership', $organization);

        $attributes = $request->validate([
            'user_id' => [
                'bail', 'required', 'integer', Rule::exists('users', 'id')->whereNull('deleted_at'),
                new ActiveOrganizationMemberRule($organization)
            ],
        ]);

        $organization->transferOwnership($request->user(), $attributes['user_id']);

        return $this->respondOk();
    }
}
