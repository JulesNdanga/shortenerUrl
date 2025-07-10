<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiSanityPostTest extends TestCase
{
    /** @test */
    public function post_to_api_stats_returns_404()
    {
        $response = $this->postJson('/api/stats/foobar', []);
        $response->assertStatus(404); // doit retourner 404 si la route POST n'existe pas
    }
}
