<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiAuthSanityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_route_is_accessible_and_returns_401_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'wrong@example.com',
            'password' => 'invalid',
        ]);
        $response->assertStatus(401);
    }

    /** @test */
    public function login_route_returns_200_with_valid_credentials()
    {
        $user = User::factory()->create(['password' => bcrypt('password')]);
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $response->assertStatus(200)->assertJsonStructure(['user', 'token']);
    }

    /** @test */
    public function shorten_route_returns_401_without_token()
    {
        $response = $this->postJson('/api/shorten', [
            'url' => 'https://laravel.com'
        ]);
        $response->assertStatus(401);
    }
}
