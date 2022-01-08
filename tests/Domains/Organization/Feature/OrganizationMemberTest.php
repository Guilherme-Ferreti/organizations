<?php

namespace Tests\Domains\Organization\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use App\Domains\Organization\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_onwers_can_add_members_to_an_onganization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $route = route('organizations.members.store', $organization);

        $this->post($route)->assertUnauthorized();

        Sanctum::actingAs($user);
        $this->post($route)->assertForbidden();

        $organization->addMember($user);
        $this->post($route)->assertForbidden();

        $organization->updateMember($user, is_technical_manager: true);
        $this->post($route)->assertForbidden();

        $organization->updateMember($user, is_owner: true, is_active: false);
        $this->post($route)->assertForbidden();

        $organization->updateMember($user, is_owner: true, is_active: true);
        $this->post($route)->assertUnprocessable();
    }

    public function test_an_member_can_be_added_to_an_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $organization->addMember($user, is_owner: true);

        $member = User::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'user_id' => $member->id,
            'is_owner' => true,
            'is_technical_manager' => true,
        ];

        $this->post(route('organizations.members.store', $organization), $payload)
            ->assertCreated();

        $payload['organization_id'] = $organization->id;

        $this->assertDatabaseHas('organization_user', $payload);
    }
}
