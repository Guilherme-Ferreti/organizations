<?php

namespace Tests\Domains\Organization\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use App\Domains\Organization\Models\Interest;
use Illuminate\Testing\Fluent\AssertableJson;
use App\Domains\Organization\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Organization\Models\OrganizationType;
use App\Domains\Organization\Models\OrganizationUser;

class OrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_organization_can_be_visualized()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $organization->addMember($user, ['is_owner' => true]);
        $organization->addMember(User::factory()->create(), ['is_technical_manager' => true]);

        Sanctum::actingAs($user);

        $this->get(route('organizations.show', $organization))
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('organization', fn (AssertableJson $json) => 
                    $json->hasAll([
                        'uuid', 'fantasy_name', 'corporate_name', 'domain', 
                        'cpf_cnpj', 'logo', 'social_contract', 'organization_type',
                        'interests', 'registered_date',
                    ])
                    ->has('members', 2, fn (AssertableJson $json) => 
                        $json->where('name', $user->name)
                            ->where('is_owner', true)
                            ->where('is_technical_manager', false)
                            ->where('is_active', true)
                    )
                )
            );
    }

    public function test_a_user_must_be_loged_in_to_create_an_organization()
    {
        $this->postJson(route('organizations.store'))
            ->assertUnauthorized();
    }

    public function test_an_organization_can_be_created()
    {
        $interest = Interest::factory()->create();
        $organization_type = OrganizationType::factory()->create();
        $user = User::factory()->create();

        Storage::fake('public');
        Sanctum::actingAs($user);

        $payload = [
            'fantasy_name'          => 'My Organization',
            'corporate_name'        => 'My Organization SA',
            'domain'                => 'organization',
            'cpf_cnpj'              => '65692965000138',
            'logo'                  => UploadedFile::fake()->image('logo.png'),
            'social_contract'       => UploadedFile::fake()->image('social_contract.png'),
            'organization_type_id'  => $organization_type->id,
            'interests'             => [$interest->name],
        ];

        $this->post(route('organizations.store'), $payload)
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('organization', fn (AssertableJson $json) => 
                    $json->hasAll([
                        'uuid', 'fantasy_name', 'corporate_name', 'domain', 
                        'cpf_cnpj', 'logo', 'social_contract', 'organization_type',
                        'interests', 'registered_date',
                    ])
                )
            );

        $payload['interests'] = json_encode($payload['interests']);
        $payload['logo'] = $payload['logo']->hashName('organizations/logos');
        $payload['social_contract'] = $payload['social_contract']->hashName('organizations/social_contracts');

        $this->assertDatabaseHas(Organization::class, $payload);
        $this->assertDatabaseHas(OrganizationUser::class, [
           'organization_id'        => 1,
           'user_id'                => $user->id,
           'is_owner'               => true,
           'is_active'              => true,
        ]);

        Storage::disk('public')
            ->assertExists([$payload['logo'], $payload['social_contract']]);
    }

    public function test_an_organization_can_be_updated()
    {
        $interest = Interest::factory()->create();
        $organization_type = OrganizationType::factory()->create();
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $organization->addMember($user, ['is_owner' => true]);

        Storage::fake('public');
        Sanctum::actingAs($user);

        $payload = [
            'fantasy_name'          => 'My Organization',
            'corporate_name'        => 'My Organization SA',
            'domain'                => 'organization',
            'cpf_cnpj'              => '65692965000138',
            'logo'                  => UploadedFile::fake()->image('logo.png'),
            'social_contract'       => UploadedFile::fake()->image('social_contract.png'),
            'organization_type_id'  => $organization_type->id,
            'interests'             => [$interest->name],
        ];

        $this->put(route('organizations.update', $organization), $payload)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('organization', fn (AssertableJson $json) => 
                    $json->hasAll([
                        'uuid', 'fantasy_name', 'corporate_name', 'domain', 
                        'cpf_cnpj', 'logo', 'social_contract', 'organization_type',
                        'interests', 'registered_date',
                    ])
                )
            );

        $payload['interests'] = json_encode($payload['interests']);
        $payload['logo'] = $payload['logo']->hashName('organizations/logos');
        $payload['social_contract'] = $payload['social_contract']->hashName('organizations/social_contracts');

        $this->assertDatabaseHas(Organization::class, $payload);

        Storage::disk('public')
            ->assertExists([$payload['logo'], $payload['social_contract']]);
    }

    public function test_only_active_owners_can_update_the_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $route = route('organizations.update', $organization);

        $this->put($route)->assertUnauthorized();

        Sanctum::actingAs($user);
        $this->put($route)->assertForbidden();

        $organization->addMember($user);
        $this->put($route)->assertForbidden();

        $organization->updateMember($user, ['is_technical_manager' => true]);
        $this->put($route)->assertForbidden();

        $organization->updateMember($user, ['is_owner' => true, 'is_active' => false]);
        $this->put($route)->assertForbidden();

        $organization->updateMember($user, ['is_owner' => true, 'is_active' => true]);
        $this->put($route)->assertOk();
    }
    
    public function test_only_active_owners_can_delete_the_organization()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();
        $route = route('organizations.destroy', $organization);

        $this->delete($route)->assertUnauthorized();

        Sanctum::actingAs($user);
        $this->delete($route)->assertForbidden();

        $organization->addMember($user);
        $this->delete($route)->assertForbidden();

        $organization->updateMember($user, ['is_technical_manager' => true]);
        $this->delete($route)->assertForbidden();

        $organization->updateMember($user, ['is_owner' => true, 'is_active' => false]);
        $this->delete($route)->assertForbidden();

        $organization->updateMember($user, ['is_owner' => true, 'is_active' => true]);
        $this->delete($route)->assertNoContent();
    }

    public function test_an_organization_can_be_deleted()
    {
        $user = User::factory()->create();
        $organization = Organization::factory()->create();

        $organization->addMember($user, ['is_owner' => true]);

        Sanctum::actingAs($user);

        $this->delete(route('organizations.destroy', $organization))
            ->assertNoContent();

        $this->assertSoftDeleted(Organization::class, [
            'id' => $organization->id,
        ]);
    }
}
