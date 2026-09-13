<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_forbids_a_convenor_with_message(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $response = $this->getJson('/api/v1/admin/users');

        $response->assertForbidden();
        $response->assertJsonPath('message', 'Sorry, only admins can view this information.');
    }

    public function test_index_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/v1/admin/users')->assertOk();
    }

    public function test_show_all_active_admins_forbids_a_regular_admin_with_message(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $response = $this->getJson('/api/v1/admin/users/admins');

        $response->assertForbidden();
        $response->assertJsonPath('message', 'Sorry, only super admins can view this information.');
    }

    public function test_show_all_active_admins_allows_a_super_admin(): void
    {
        Sanctum::actingAs(User::factory()->superAdmin()->create());

        $this->getJson('/api/v1/admin/users/admins')->assertOk();
    }

    public function test_destroy_forbids_a_convenor_with_message(): void
    {
        $target = User::factory()->externalTa()->create();

        Sanctum::actingAs(User::factory()->convenor()->create());

        $response = $this->deleteJson("/api/v1/admin/users/{$target->email}");

        $response->assertForbidden();
        $response->assertJsonPath('message', 'Sorry, only admins can delete users.');
        $this->assertDatabaseHas('users', ['email' => $target->email]);
    }

    public function test_destroy_allows_an_admin(): void
    {
        $target = User::factory()->externalTa()->create();

        Sanctum::actingAs(User::factory()->admin()->create());

        $this->deleteJson("/api/v1/admin/users/{$target->email}")->assertOk();
        $this->assertDatabaseMissing('users', ['email' => $target->email]);
    }
}
