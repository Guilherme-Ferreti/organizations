<?php

namespace Tests\Feature;

use App\Domains\Organization\Models\Interest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class InterestTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');
    }

    public function test_a_list_of_all_interests_is_returned()
    {
        Sanctum::actingAs(User::first());

        $this->getJson(route('organizations.interests.index'))
            ->assertOk()
            ->assertExactJson([
                'interests' => Interest::all()->pluck('name'),
            ]);
    }
}
