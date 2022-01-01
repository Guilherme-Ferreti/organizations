<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_an_user_can_register()
    {
        $payload = [
            'name'                      => $this->faker()->name(),
            'email'                     => $this->faker()->safeEmail(),
            'password'                  => 'password',
            'password_confirmation'     => 'password',
        ];

        $this->postJson(route('auth.register'), $payload)
            ->assertCreated()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('user', fn (AssertableJson $json) =>
                    $json
                        ->has('id')
                        ->where('name', $payload['name'])
                        ->where('email', $payload['email'])
                        ->missing('password')
                        ->etc()
                )
            );

        $this->assertDatabaseHas('users', [
            'name'  => $payload['name'],
            'email' => $payload['email'],
        ]);
    }

    public function test_an_user_can_login()
    {
        $user = User::factory()->create();

        $payload = [
            'email'     => $user->email,
            'password'  => 'password',
        ];

        $this->postJson(route('auth.login'), $payload)
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => 
                $json->has('accessToken')
            );

        $this->assertSame(1, $user->tokens()->where('name', 'access-token')->count());
    }

    public function test_a_user_can_logout()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->postJson(route('auth.logout'))
            ->assertOk();

        $this->assertSame(0, $user->tokens()->where('name', 'access-token')->count());
    }

    public function test_a_guest_cannot_logout()
    {
        $this->postJson(route('auth.logout'))
            ->assertUnauthorized();
    }
}
