<?php

namespace Tests\Domains\Organization\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Models\OrganizationUser;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrganizationMemberTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_owners_can_add_members_to_an_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $route = route('organizations.members.store', $organization);

        $this->authorizationAssertions($user, $organization, $route, 'post');
    }

    public function test_an_member_can_be_added_to_an_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $organization->addMember($user, ['is_owner' => true]);

        $member = User::factory()->create();

        Sanctum::actingAs($user);

        $payload = [
            'user_id' => $member->id,
            'is_owner' => true,
            'is_technical_manager' => true,
        ];

        $this->postJson(route('organizations.members.store', $organization), $payload)
            ->assertCreated();

        $payload['organization_id'] = $organization->id;

        $this->assertDatabaseHas(OrganizationUser::class, $payload);
    }

    public function test_only_active_owners_can_update_members_of_an_organization()
    {
        $user = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create();

        $organization->addMember($user);
        $organization->addMember($member);

        $route = route('organizations.members.update', [$organization, $member]);

        $this->authorizationAssertions($user, $organization, $route, 'put');
    }

    public function test_an_member_of_an_organization_can_be_updated()
    {
        $user = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create();

        $organization->addMember($user, ['is_owner' => true]);
        $organization->addMember($member);

        Sanctum::actingAs($user);

        $payload = [
            'is_owner' => true,
            'is_technical_manager' => true,
            'is_active' => false,
        ];

        $this->putJson(route('organizations.members.update', [$organization, $member]), $payload)
            ->assertOk();

        $payload['organization_id'] = $organization->id;
        $payload['user_id'] = $member->id;

        $this->assertDatabaseHas(OrganizationUser::class, $payload);
    }

    public function test_only_active_owners_can_remove_members_from_an_organization()
    {
        $user = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create();

        $organization->addMember($user);
        $organization->addMember($member);

        $route = route('organizations.members.destroy', [$organization, $member]);

        $this->authorizationAssertions($user, $organization, $route, 'delete');
    }

    public function test_an_owner_cannot_be_removed_from_an_organization()
    {
        $user = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create();

        $organization->addMember($user, ['is_owner' => true]);
        $organization->addMember($member, ['is_owner' => true]);

        Sanctum::actingAs($user);

        $this->delete(route('organizations.members.destroy', [$organization, $member]))
            ->assertForbidden();
    }

    public function test_an_member_can_be_removed_from_an_organization()
    {
        $user = User::factory()->create();
        $member = User::factory()->create();
        $organization = Organization::factory()->create();

        $organization->addMember($user, ['is_owner' => true]);
        $organization->addMember($member);

        Sanctum::actingAs($user);

        $this->delete(route('organizations.members.destroy', [$organization, $member]))
            ->assertNoContent();

        $this->assertDatabaseMissing(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
        ]);
    }

    public function test_only_active_owners_can_transfer_an_organization_ownership()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $route = route('organizations.members.transfer_ownership', $organization);

        $this->authorizationAssertions($user, $organization, $route, 'patch');
    }

    public function test_an_organization_ownership_can_be_transfered_only_to_another_active_member()
    {
        $organization = Organization::factory()->create();
        $owner = User::factory()->create();
        $user = User::factory()->create();

        $organization->addMember($owner, ['is_owner' => true]);

        Sanctum::actingAs($owner);

        $this->patchJson(route('organizations.members.transfer_ownership', $organization), [
            'user_id' => $user->id,
        ])->assertUnprocessable();

        $organization->addMember($user, ['is_active' => false]);

        $this->patchJson(route('organizations.members.transfer_ownership', $organization), [
            'user_id' => $user->id,
        ])->assertUnprocessable();
    }

    public function test_an_owner_can_transfer_an_organization_ownership()
    {
        $organization = Organization::factory()->create();
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $organization->addMember($owner, ['is_owner' => true]);
        $organization->addMember($member);

        Sanctum::actingAs($owner);

        $this->patchJson(route('organizations.members.transfer_ownership', $organization), [
            'user_id' => $member->id,
        ])->assertOk();

        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $member->id,
            'is_owner' => true,
        ]);
        
        $this->assertDatabaseHas(OrganizationUser::class, [
            'organization_id' => $organization->id,
            'user_id' => $owner->id,
            'is_owner' => false,
        ]);
    }

    private function authorizationAssertions(User $user, Organization $organization, string $route, string $method)
    {
        $this->{$method}($route)->assertUnauthorized();

        Sanctum::actingAs($user);
        $this->{$method}($route)->assertForbidden();

        $organization->addMember($user);
        $this->{$method}($route)->assertForbidden();

        $organization->updateMember($user, ['is_technical_manager' => true]);
        $this->{$method}($route)->assertForbidden();

        $organization->updateMember($user, ['is_owner' => true, 'is_active' => false]);
        $this->{$method}($route)->assertForbidden();
    }
}
