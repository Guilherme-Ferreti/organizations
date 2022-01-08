<?php

namespace App\Domains\Organization\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Rules\NonOrganizationMemberRule;

class OrganizationMemberController extends Controller
{
    public function store(Request $request, Organization $organization)
    {
        $this->authorize('addMembers', $organization);

        $attributes = $request->validate([
            'user_id' => [
                'required', 'integer', Rule::exists('users', 'id')->where('deleted_at', null),
                new NonOrganizationMemberRule($organization)
            ],
            'is_technical_manager' => ['boolean'],
            'is_owner' => ['boolean'],
        ]);

        $organization->addMember(
            $attributes['user_id'], 
            $attributes['is_technical_manager'] ?? false, 
            $attributes['is_owner'] ?? false
        );

        return $this->respondCreated();
    }
}
