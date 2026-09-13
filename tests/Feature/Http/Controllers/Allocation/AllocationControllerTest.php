<?php

namespace Tests\Feature\Http\Controllers\Allocation;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AllocationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_forbids_a_ta(): void
    {
        Sanctum::actingAs(User::factory()->externalTa()->create());

        $this->getJson('/api/v1/admin/dashboard')->assertForbidden();
    }

    public function test_dashboard_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/v1/admin/dashboard')->assertOk();
    }

    public function test_store_forbids_a_convenor(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $this->postJson('/api/v1/admin/allocations')->assertForbidden();
    }

    public function test_destroy_current_forbids_a_ta(): void
    {
        Sanctum::actingAs(User::factory()->externalTa()->create());

        $this->deleteJson('/api/v1/admin/allocations/current')->assertForbidden();
    }

    public function test_destroy_current_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        // No allocation exists yet, so the 404 below (rather than a 403) proves authorization passed.
        $response = $this->deleteJson('/api/v1/admin/allocations/current');

        $response->assertNotFound();
        $response->assertJsonPath('message', 'No allocation found for the current semester.');
    }
}
