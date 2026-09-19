<?php

namespace Tests\Feature\Http\Controllers\Allocation;

use App\Models\Allocation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AllocationControllerTest extends TestCase
{
    use RefreshDatabase;

    private const ACADEMIC_YEAR = '2020-2021-01';

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

    public function test_store_resolves_contention_by_awarding_the_higher_weight_ta_and_excluding_the_lower_weight_ta(): void
    {
        $admin = User::factory()->admin()->create();
        $convenor = User::factory()->convenor()->create();

        Sanctum::actingAs($admin);
        $this->postJson('/api/v1/admin/modules', [
            'module_id' => 'CS101',
            'module_name' => 'Intro to CS',
            'academic_year' => self::ACADEMIC_YEAR,
            'convenor_email' => $convenor->email,
        ])->assertCreated();

        Sanctum::actingAs($convenor);
        $this->postJson('/api/v1/module-preferences', [
            'module_id' => 'CS101',
            'academic_year' => self::ACADEMIC_YEAR,
            'no_of_assistants' => 1,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 6,
        ])->assertCreated();

        $strongerTa = User::factory()->externalTa()->create();
        $weakerTa = User::factory()->externalTa()->create();

        // Real prior-year allocation history gives the stronger TA an objective repetition-weight
        // bonus over the weaker TA, who is otherwise an identical proposer for the same module.
        Allocation::factory()->create([
            'academic_year' => '2020-2021-02',
            'allocation_id' => '2020-2021-02-A-01',
            'ta_id' => $strongerTa->email,
            'module_id' => 'CS101',
        ]);

        foreach ([$strongerTa, $weakerTa] as $ta) {
            Sanctum::actingAs($ta);
            $this->postJson('/api/v1/ta-preferences', [
                'module_1_id' => 'CS101',
                'max_modules' => 1,
                'max_contact_hours' => 10,
                'max_marking_hours' => 10,
                'academic_year' => self::ACADEMIC_YEAR,
            ])->assertCreated();
        }

        Sanctum::actingAs($admin);
        $allocateResponse = $this->postJson('/api/v1/admin/allocations');
        $allocateResponse->assertCreated();
        $allocationId = $allocateResponse->json('allocation_id');

        $this->assertDatabaseHas('allocations', [
            'allocation_id' => $allocationId,
            'module_id' => 'CS101',
            'ta_id' => $strongerTa->email,
        ]);
        $this->assertDatabaseMissing('allocations', [
            'allocation_id' => $allocationId,
            'module_id' => 'CS101',
            'ta_id' => $weakerTa->email,
        ]);
    }
}
