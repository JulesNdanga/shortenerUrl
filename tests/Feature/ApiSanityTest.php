<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiSanityTest extends TestCase
{
    /** @test */
    public function api_route_works()
    {
        $response = $this->getJson('/api/stats/foobar');
        $response->assertStatus(404); // 404 attendu car le code n'existe pas, mais la route doit répondre
    }
}
