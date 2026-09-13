<?php

namespace Tests\Feature\Http\Controllers\Prefs;

use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ModuleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_forbids_a_ta(): void
    {
        Sanctum::actingAs(User::factory()->externalTa()->create());

        $this->getJson('/api/v1/module-preferences')->assertForbidden();
    }

    public function test_index_allows_a_convenor(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $this->getJson('/api/v1/module-preferences')->assertOk();
    }

    public function test_store_forbids_a_convenor_who_does_not_own_the_module(): void
    {
        $owner = User::factory()->convenor()->create();
        $otherConvenor = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($otherConvenor);

        $response = $this->postJson('/api/v1/module-preferences', [
            'module_id' => 'CS101',
            'academic_year' => '2020-2021-01',
            'no_of_assistants' => 2,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 3,
        ]);

        $response->assertForbidden();
        $response->assertJsonPath('message', "You are not authorized to modify this module's preferences.");
        $this->assertDatabaseMissing('module_preferences', ['module_id' => 'CS101']);
    }

    public function test_store_allows_the_owning_convenor(): void
    {
        $owner = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($owner);

        $response = $this->postJson('/api/v1/module-preferences', [
            'module_id' => 'CS101',
            'academic_year' => '2020-2021-01',
            'no_of_assistants' => 2,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 3,
        ]);

        $response->assertCreated();
        $response->assertJsonPath('message', 'Preference saved.');
        $this->assertDatabaseHas('module_preferences', ['module_id' => 'CS101', 'academic_year' => '2020-2021-01']);
    }

    public function test_store_forbids_a_ta(): void
    {
        $owner = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs(User::factory()->externalTa()->create());

        $response = $this->postJson('/api/v1/module-preferences', [
            'module_id' => 'CS101',
            'academic_year' => '2020-2021-01',
            'no_of_assistants' => 2,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 3,
        ]);

        $response->assertForbidden();
    }

    public function test_show_reports_editable_true_for_the_owning_convenor(): void
    {
        $owner = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($owner);

        $response = $this->getJson('/api/v1/module-preferences/CS101/2020-2021-01');

        $response->assertOk();
        $response->assertJsonPath('editable', true);
    }

    public function test_show_reports_editable_false_for_a_convenor_who_does_not_own_the_module(): void
    {
        $owner = User::factory()->convenor()->create();
        $otherConvenor = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($otherConvenor);

        $response = $this->getJson('/api/v1/module-preferences/CS101/2020-2021-01');

        $response->assertOk();
        $response->assertJsonPath('editable', false);
    }

    public function test_update_forbids_a_convenor_who_does_not_own_the_module(): void
    {
        $owner = User::factory()->convenor()->create();
        $otherConvenor = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($otherConvenor);

        $response = $this->putJson('/api/v1/module-preferences/CS101/2020-2021-01', [
            'no_of_assistants' => 2,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 3,
        ]);

        $response->assertForbidden();
    }

    public function test_update_allows_the_owning_convenor(): void
    {
        $owner = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($owner);

        $response = $this->putJson('/api/v1/module-preferences/CS101/2020-2021-01', [
            'no_of_assistants' => 2,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 3,
        ]);

        // Ownership is checked before the "did this module submit preferences" business rule,
        // so an owning convenor reaches the 404 below instead of being forbidden.
        $response->assertNotFound();
        $response->assertJsonPath('message', 'This module did not submit preferences for this academic year.');
    }

    public function test_destroy_forbids_a_convenor_who_does_not_own_the_module(): void
    {
        $owner = User::factory()->convenor()->create();
        $otherConvenor = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($otherConvenor);

        $this->deleteJson('/api/v1/module-preferences/CS101/2020-2021-01')->assertForbidden();
    }

    public function test_destroy_allows_the_owning_convenor(): void
    {
        $owner = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $owner->email, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($owner);

        $this->deleteJson('/api/v1/module-preferences/CS101/2020-2021-01')->assertOk();
    }
}
