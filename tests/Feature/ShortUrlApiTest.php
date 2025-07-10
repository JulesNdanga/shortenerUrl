<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlApiTest extends TestCase
{
    use RefreshDatabase;


    use RefreshDatabase;

    public function test_authenticated_user_can_shorten_url()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/shorten', [
                'url' => 'https://laravel.com',
            ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['short_url', 'original_url', 'short_code']);
    }

    public function test_guest_cannot_shorten_url()
    {
        $response = $this->postJson('/api/shorten', [
            'url' => 'https://laravel.com',
        ]);
        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_history()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/shorten', [
                'url' => 'https://laravel.com',
            ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                ['short_code', 'original_url', 'created_at', 'click_count', 'expires_at']
            ]);
    }
}
