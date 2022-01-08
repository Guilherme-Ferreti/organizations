<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use App\Domains\Organization\Models\Interest;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Rules\ActiveOrganizationMemberRule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Organization\Rules\ExistingInterestName;
use App\Domains\Organization\Rules\NonOrganizationMemberRule;

class RuleTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        
        Artisan::call('db:seed');
    }

    public function test_rule_existing_interest_name_works_correctly()
    {
        $rules = ['interest_name' => new ExistingInterestName];
        $data = ['interest_name' => 'invalid name'];

        $this->assertTrue(Validator::make($data, $rules)->fails());

        $data['interest_name'] = Interest::inRandomOrder()->first()->name;
        $this->assertFalse(Validator::make($data, $rules)->fails());
    }

    public function test_rule_active_organization_member_works_correctly()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $rules = ['user_id' => new ActiveOrganizationMemberRule($organization)];
        $data = ['user_id' => $user->id];

        $this->assertTrue(Validator::make($data, $rules)->fails());

        $organization->addMember($user, is_active: false);
        $this->assertTrue(Validator::make($data, $rules)->fails());
        
        $organization->updateMember($user, is_active: true);
        $this->assertFalse(Validator::make($data, $rules)->fails());
    }
    
    public function test_rule_non_organization_member_works_correctly()
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create();

        $rules = ['user_id' => new NonOrganizationMemberRule($organization)];
        $data = ['user_id' => $user->id];

        $organization->addMember($user);
        $this->assertTrue(Validator::make($data, $rules)->fails());

        $organization->removeMember($user);
        $this->assertFalse(Validator::make($data, $rules)->fails());
    }
}
