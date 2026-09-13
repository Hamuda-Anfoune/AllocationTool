<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ModuleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_forbids_a_convenor(): void
    {
        $convenor = User::factory()->convenor()->create();
        Sanctum::actingAs($convenor);

        $response = $this->postJson('/api/v1/admin/modules', [
            'module_id' => 'CS101',
            'module_name' => 'Intro to CS',
            'academic_year' => '2020-2021-01',
            'convenor_email' => $convenor->email,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('modules', ['module_id' => 'CS101']);
    }

    public function test_store_allows_an_admin(): void
    {
        $convenor = User::factory()->convenor()->create();
        Sanctum::actingAs(User::factory()->admin()->create());

        $response = $this->postJson('/api/v1/admin/modules', [
            'module_id' => 'CS101',
            'module_name' => 'Intro to CS',
            'academic_year' => '2020-2021-01',
            'convenor_email' => $convenor->email,
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('modules', ['module_id' => 'CS101', 'convenor_email' => $convenor->email]);
    }
}
