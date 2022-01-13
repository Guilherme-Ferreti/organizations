<?php

namespace App\Domains\Organization\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\Organization\Models\Invitation;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Rules\NonOrganizationMemberRule;
use App\Domains\Organization\Notifications\InvitationNotification;
use App\Domains\Organization\Rules\MissingInvitationRule;

class InvitationController extends Controller
{
    public function store(Request $request, Organization $organization)
    {
        $this->authorize('create', [Invitation::class, $organization]);

        $attributes = $request->validate([
            'invitations'           => ['required', 'array'],
            'invitations.*.user_id' => [
                'required', 'integer', 'distinct', 'exists:users,id', 
                new NonOrganizationMemberRule($organization), new MissingInvitationRule($organization)
            ],
        ]);

        foreach ($attributes['invitations'] as $invitation) {
            $invitation = Invitation::create($invitation);

            User::find($invitation['user_id'])->notify(new InvitationNotification($invitation, $organization));
        }

        return $this->respondOk();
    }
}
