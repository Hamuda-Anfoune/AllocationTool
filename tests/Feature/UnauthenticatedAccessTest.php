<?php

namespace Tests\Feature;

use Tests\TestCase;

class UnauthenticatedAccessTest extends TestCase
{
    public function test_protected_api_route_returns_json_401_without_a_redirect(): void
    {
        $response = $this->getJson('/api/v1/user');

        $response->assertUnauthorized();
        $response->assertJsonPath('message', 'Unauthenticated.');
        $response->assertHeaderMissing('Location');
    }

    public function test_dropped_web_welcome_route_no_longer_exists(): void
    {
        $response = $this->get('/');

        $response->assertNotFound();
    }
}
