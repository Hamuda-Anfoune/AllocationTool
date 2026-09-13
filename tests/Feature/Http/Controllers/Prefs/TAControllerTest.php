<?php

namespace Tests\Feature\Http\Controllers\Prefs;

use App\Models\Module;
use App\Models\TaPreference;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TAControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_forbids_a_convenor(): void
    {
        Sanctum::actingAs(User::factory()->convenor()->create());

        $this->postJson('/api/v1/ta-preferences', [])->assertForbidden();
    }

    public function test_store_allows_an_external_ta(): void
    {
        $convenor = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $convenor->email, 'academic_year' => '2020-2021-01']);
        $ta = User::factory()->externalTa()->create();

        Sanctum::actingAs($ta);

        $response = $this->postJson('/api/v1/ta-preferences', [
            'module_1_id' => 'CS101',
            'max_modules' => 2,
            'max_contact_hours' => 6,
            'max_marking_hours' => 8,
            'academic_year' => '2020-2021-01',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('ta_preferences', ['ta_email' => $ta->email, 'academic_year' => '2020-2021-01']);
    }

    public function test_show_forbids_a_ta_who_does_not_own_the_preference(): void
    {
        $owner = User::factory()->externalTa()->create();
        TaPreference::create(['preference_id' => 'p1', 'ta_email' => $owner->email, 'max_modules' => 1, 'max_contact_hours' => 1, 'max_marking_hours' => 7, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs(User::factory()->externalTa()->create());

        $this->getJson('/api/v1/ta-preferences/p1')->assertForbidden();
    }

    public function test_show_allows_the_owning_ta(): void
    {
        $owner = User::factory()->externalTa()->create();
        TaPreference::create(['preference_id' => 'p1', 'ta_email' => $owner->email, 'max_modules' => 1, 'max_contact_hours' => 1, 'max_marking_hours' => 7, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($owner);

        $this->getJson('/api/v1/ta-preferences/p1')->assertOk();
    }

    public function test_show_allows_an_admin_for_any_preference(): void
    {
        $owner = User::factory()->externalTa()->create();
        TaPreference::create(['preference_id' => 'p1', 'ta_email' => $owner->email, 'max_modules' => 1, 'max_contact_hours' => 1, 'max_marking_hours' => 7, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs(User::factory()->admin()->create());

        $this->getJson('/api/v1/ta-preferences/p1')->assertOk();
    }

    public function test_update_forbids_a_ta_who_does_not_own_the_preference(): void
    {
        $owner = User::factory()->externalTa()->create();
        TaPreference::create(['preference_id' => 'p1', 'ta_email' => $owner->email, 'max_modules' => 1, 'max_contact_hours' => 1, 'max_marking_hours' => 7, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs(User::factory()->externalTa()->create());

        $this->putJson('/api/v1/ta-preferences/p1', [])->assertForbidden();
    }

    public function test_update_allows_the_owning_ta(): void
    {
        $owner = User::factory()->externalTa()->create();
        $convenor = User::factory()->convenor()->create();
        Module::create(['module_id' => 'CS101', 'module_name' => 'Intro to CS', 'convenor_email' => $convenor->email, 'academic_year' => '2020-2021-01']);
        TaPreference::create(['preference_id' => 'p1', 'ta_email' => $owner->email, 'max_modules' => 1, 'max_contact_hours' => 1, 'max_marking_hours' => 7, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($owner);

        $response = $this->putJson('/api/v1/ta-preferences/p1', [
            'preference_id' => 'p1',
            'module_1_id' => 'CS101',
            'max_modules' => 3,
            'max_contact_hours' => 5,
            'max_marking_hours' => 9,
            'academic_year' => '2020-2021-01',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('ta_preferences', ['preference_id' => 'p1', 'max_modules' => 3]);
    }

    public function test_destroy_forbids_a_ta_who_does_not_own_the_preference(): void
    {
        $owner = User::factory()->externalTa()->create();
        TaPreference::create(['preference_id' => 'p1', 'ta_email' => $owner->email, 'max_modules' => 1, 'max_contact_hours' => 1, 'max_marking_hours' => 7, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs(User::factory()->externalTa()->create());

        $this->deleteJson('/api/v1/ta-preferences/p1')->assertForbidden();
        $this->assertDatabaseHas('ta_preferences', ['preference_id' => 'p1']);
    }

    public function test_destroy_allows_the_owning_ta(): void
    {
        $owner = User::factory()->externalTa()->create();
        TaPreference::create(['preference_id' => 'p1', 'ta_email' => $owner->email, 'max_modules' => 1, 'max_contact_hours' => 1, 'max_marking_hours' => 7, 'academic_year' => '2020-2021-01']);

        Sanctum::actingAs($owner);

        $this->deleteJson('/api/v1/ta-preferences/p1')->assertOk();
        $this->assertDatabaseMissing('ta_preferences', ['preference_id' => 'p1']);
    }
}
