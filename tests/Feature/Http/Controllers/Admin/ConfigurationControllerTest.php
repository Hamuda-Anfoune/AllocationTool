<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ConfigurationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_forbids_a_ta_with_message(): void
    {
        Sanctum::actingAs(User::factory()->externalTa()->create());

        $response = $this->getJson('/api/v1/admin/config');

        $response->assertForbidden();
        $response->assertJsonPath('message', 'You are not authorized to manage configuration.');
    }

    public function test_index_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/v1/admin/config')->assertOk();
    }

    public function test_reset_module_priority_weights_forbids_a_convenor(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $this->postJson('/api/v1/admin/config/module-priority-weights/reset')->assertForbidden();
    }

    public function test_reset_module_priority_weights_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->postJson('/api/v1/admin/config/module-priority-weights/reset')->assertOk();
    }
}
