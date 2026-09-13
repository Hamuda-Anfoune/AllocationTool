<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AcademicYearControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_forbids_a_convenor(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $response = $this->putJson('/api/v1/admin/academic-years', ['new_academic_year' => '2021-2022-01']);

        $response->assertForbidden();
        $this->assertDatabaseHas('academic_years', ['year' => '2020-2021-01', 'current' => true]);
    }

    public function test_update_allows_an_admin(): void
    {
        Sanctum::actingAs(User::factory()->admin()->create());

        $response = $this->putJson('/api/v1/admin/academic-years', ['new_academic_year' => '2021-2022-01']);

        $response->assertOk();
        $this->assertDatabaseHas('academic_years', ['year' => '2021-2022-01', 'current' => true]);
        $this->assertDatabaseHas('academic_years', ['year' => '2020-2021-01', 'current' => false]);
    }
}
