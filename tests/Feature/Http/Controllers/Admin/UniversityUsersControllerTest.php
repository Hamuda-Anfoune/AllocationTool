<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UniversityUsersControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_forbids_a_convenor(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $this->getJson('/api/v1/admin/university-users')->assertForbidden();
    }

    public function test_index_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/v1/admin/university-users')->assertOk();
    }

    public function test_store_forbids_a_convenor(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $response = $this->postJson('/api/v1/admin/university-users', [
            'email' => 'new-ta@example.com',
            'account_type_id' => '003',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('university_users', ['email' => 'new-ta@example.com']);
    }

    public function test_store_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $response = $this->postJson('/api/v1/admin/university-users', [
            'email' => 'new-ta@example.com',
            'account_type_id' => '003',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('university_users', ['email' => 'new-ta@example.com', 'account_type_id' => '003']);
    }
}
