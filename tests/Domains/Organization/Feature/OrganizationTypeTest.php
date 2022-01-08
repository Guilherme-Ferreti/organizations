<?php

namespace Tests\Domains\Organization\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Domains\Organization\Models\OrganizationType;

class OrganizationTypeTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');
    }

    public function test_a_list_of_all_types_is_returned()
    {
        Sanctum::actingAs(User::first());

        $this->getJson(route('organizations.types.index'))
            ->assertOk()
            ->assertExactJson([
                'organization_types' => OrganizationType::all(),
            ]);
    }
}
