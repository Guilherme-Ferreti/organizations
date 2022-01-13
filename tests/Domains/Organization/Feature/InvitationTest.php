<?php

namespace Tests\Domains\Organization\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use App\Domains\Organization\Models\Invitation;
use App\Domains\Organization\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Organization\Notifications\InvitationNotification;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_active_owners_can_invite_users_to_an_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $route = route('organizations.invitations.store', $organization);

        $this->authorizationAssertions($user, $organization, $route, 'post');
    }

    public function test_invitations_can_be_send()
    {
        $users = User::factory(5)->create();

        $owner = User::factory()->create();
        $organization = Organization::factory()->create();
        $organization->addMember($owner, ['is_owner' => true]);

        Sanctum::actingAs($owner);
        Notification::fake();

        foreach ($users as $user) {
            $payload['invitations'][] = ['user_id' => $user->id];
        }

        $this->postJson(route('organizations.invitations.store', $organization), $payload)
            ->assertOk();

        foreach ($users as $user) {
            Notification::assertSentTo($user, InvitationNotification::class);

            $this->assertDatabaseHas(Invitation::class, [
                'user_id' => $user->id,
                'organization_id' => $organization->id,
            ]);
        }
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
