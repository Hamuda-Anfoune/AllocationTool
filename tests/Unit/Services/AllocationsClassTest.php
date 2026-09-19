<?php

namespace Tests\Unit\Services;

use App\Models\Module;
use App\Models\ModulePreference;
use App\Models\ModuleRankOrderList;
use App\Models\TaModuleChoice;
use App\Models\TaPreference;
use App\Models\User;
use App\Services\AllocationsClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests pinning down the behavior of AllocationsClass's ROL-building methods.
 */
class AllocationsClassTest extends TestCase
{
    use RefreshDatabase;

    private const ACADEMIC_YEAR = '2020-2021-01';

    public function test_load_weights_for_year_returns_full_weight_table_keyed_by_module_then_ta(): void
    {
        $module = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);
        ModulePreference::factory()->create([
            'module_id' => $module->module_id,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);

        $ta = User::factory()->graduateTa()->create();

        ModuleRankOrderList::factory()->create([
            'module_id' => $module->module_id,
            'ta_email' => $ta->email,
            'ta_total_weight' => 42,
            'module_priority_for_ta' => 3,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);

        $weights = app(AllocationsClass::class)->loadWeightsForYear(self::ACADEMIC_YEAR);

        $this->assertSame(42.0, $weights[$module->module_id][$ta->email]['weight']);
        $this->assertSame(3, $weights[$module->module_id][$ta->email]['module_priority_for_ta']);
    }

    public function test_create_final_rols_for_modules_includes_every_ranked_ta_ordered_by_weight_desc(): void
    {
        $module = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);
        ModulePreference::factory()->create([
            'module_id' => $module->module_id,
            'no_of_assistants' => 1,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 6,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);

        $lowWeightTa = User::factory()->graduateTa()->create();
        $highWeightTa = User::factory()->graduateTa()->create();

        ModuleRankOrderList::factory()->create([
            'module_id' => $module->module_id,
            'ta_email' => $lowWeightTa->email,
            'ta_total_weight' => 30,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);
        ModuleRankOrderList::factory()->create([
            'module_id' => $module->module_id,
            'ta_email' => $highWeightTa->email,
            'ta_total_weight' => 80,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);

        $rols = app(AllocationsClass::class)->createFinalRolsForModulesForYear(self::ACADEMIC_YEAR);

        // No top-N truncation: every ranked TA is present, not just the top `no_of_assistants`,
        // since a module's bump eligibility must consider its full ranking.
        $this->assertCount(2, $rols[$module->module_id]['tas']);
        $this->assertSame(1, $rols[$module->module_id]['tas'][$highWeightTa->email]['ta_priority']);
        $this->assertSame(2, $rols[$module->module_id]['tas'][$lowWeightTa->email]['ta_priority']);
        $this->assertSame(6.0, $rols[$module->module_id]['marking_hours']);
    }

    public function test_create_tas_rols_and_prefs_orders_preferred_modules_by_priority_then_appends_the_rest(): void
    {
        $ta = User::factory()->graduateTa()->create();
        $preference = TaPreference::factory()->create(['ta_email' => $ta->email, 'max_modules' => 3, 'academic_year' => self::ACADEMIC_YEAR]);

        $firstChoice = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);
        $secondChoice = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);
        $unchosen = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);

        TaModuleChoice::factory()->create([
            'preference_id' => $preference->preference_id,
            'ta_email' => $ta->email,
            'module_id' => $secondChoice->module_id,
            'priority' => 2,
        ]);
        TaModuleChoice::factory()->create([
            'preference_id' => $preference->preference_id,
            'ta_email' => $ta->email,
            'module_id' => $firstChoice->module_id,
            'priority' => 1,
        ]);

        $rols = app(AllocationsClass::class)->createTasRolsAndPrefsForYear(self::ACADEMIC_YEAR);

        $modules = $rols[$ta->email]['modules'];

        $this->assertSame($firstChoice->module_id, $modules[1]['module_id']);
        $this->assertSame($secondChoice->module_id, $modules[2]['module_id']);
        $this->assertContains(['module_id' => $unchosen->module_id], $modules);
    }
}
