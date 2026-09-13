<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TAControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_forbids_a_convenor(): void
    {
        $target = User::factory()->externalTa()->create();

        Sanctum::actingAs(User::factory()->convenor()->create());

        $this->getJson("/api/v1/admin/ta-preferences/{$target->email}")->assertForbidden();
    }

    public function test_show_forbids_a_ta_viewing_another_ta_with_message(): void
    {
        $target = User::factory()->externalTa()->create();

        Sanctum::actingAs(User::factory()->externalTa()->create());

        $response = $this->getJson("/api/v1/admin/ta-preferences/{$target->email}");

        $response->assertForbidden();
        $response->assertJsonPath('message', 'Sorry, only admins and teaching assistants can view this information.');
    }

    public function test_show_allows_an_admin_for_any_ta(): void
    {
        $target = User::factory()->externalTa()->create();

        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson("/api/v1/admin/ta-preferences/{$target->email}")->assertOk();
    }

    public function test_show_allows_a_ta_viewing_their_own_preferences(): void
    {
        $ta = User::factory()->externalTa()->create();

        Sanctum::actingAs($ta);

        $this->getJson("/api/v1/admin/ta-preferences/{$ta->email}")->assertOk();
    }
}
