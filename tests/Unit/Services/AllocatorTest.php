<?php

namespace Tests\Unit\Services;

use App\Models\AcademicYear;
use App\Models\Allocation;
use App\Models\Module;
use App\Models\ModulePreference;
use App\Models\ModuleRankOrderList;
use App\Models\TaLanguageChoice;
use App\Models\TaModuleChoice;
use App\Models\TaPreference;
use App\Models\UsedLanguage;
use App\Models\User;
use App\Services\Allocator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests pinning down the behavior of Allocator, including the real-history-based
 * repetition weight and a query-count regression guard on createModuleROLs().
 */
class AllocatorTest extends TestCase
{
    use RefreshDatabase;

    private const ACADEMIC_YEAR = '2020-2021-01';

    private const PRIOR_ACADEMIC_YEAR = '2020-2021-02';

    public function test_create_module_rols_computes_total_weight_from_priority_repetition_and_language_weights(): void
    {
        $this->assertSame(self::ACADEMIC_YEAR, AcademicYear::currentYear());

        $module = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);
        ModulePreference::factory()->create([
            'module_id' => $module->module_id,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);
        UsedLanguage::factory()->create([
            'module_id' => $module->module_id,
            'language_id' => '001',
            'priority' => 1,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);

        $ta = User::factory()->graduateTa()->create();
        $preference = TaPreference::factory()->create(['ta_email' => $ta->email, 'academic_year' => self::ACADEMIC_YEAR]);
        TaModuleChoice::factory()->create([
            'preference_id' => $preference->preference_id,
            'ta_email' => $ta->email,
            'module_id' => $module->module_id,
            'priority' => 1,
        ]);
        TaLanguageChoice::factory()->create([
            'preference_id' => $preference->preference_id,
            'language_id' => '001',
        ]);

        // Real prior-year allocation history: one repetition, weight 10.
        Allocation::factory()->create([
            'academic_year' => self::PRIOR_ACADEMIC_YEAR,
            'allocation_id' => self::PRIOR_ACADEMIC_YEAR.'-A-01',
            'ta_id' => $ta->email,
            'module_id' => $module->module_id,
        ]);

        app(Allocator::class)->createModuleROLs();

        $rol = ModuleRankOrderList::where('module_id', $module->module_id)->where('ta_email', $ta->email)->first();

        $this->assertNotNull($rol);
        $this->assertSame(1, $rol->module_priority_for_ta);
        $this->assertSame(27.0, (float) $rol->module_priority_for_ta_weight);
        $this->assertSame(10.0, (float) $rol->did_before_weight);
        $this->assertSame(50.0, (float) $rol->languages_similarity_weight);
        $this->assertSame(87.0, (float) $rol->ta_total_weight);
    }

    public function test_create_module_rols_awards_no_repetition_weight_when_ta_has_no_allocation_history(): void
    {
        $module = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);
        ModulePreference::factory()->create([
            'module_id' => $module->module_id,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);

        $ta = User::factory()->graduateTa()->create();
        $preference = TaPreference::factory()->create(['ta_email' => $ta->email, 'academic_year' => self::ACADEMIC_YEAR]);
        TaModuleChoice::factory()->create([
            'preference_id' => $preference->preference_id,
            'ta_email' => $ta->email,
            'module_id' => $module->module_id,
            'priority' => 1,
        ]);

        app(Allocator::class)->createModuleROLs();

        $rol = ModuleRankOrderList::where('module_id', $module->module_id)->where('ta_email', $ta->email)->first();

        $this->assertSame(0.0, (float) $rol->did_before_weight);
        $this->assertSame(27.0, (float) $rol->ta_total_weight);
    }

    public function test_create_module_rols_excludes_current_year_allocations_from_repetition_weight(): void
    {
        $module = Module::factory()->create(['academic_year' => self::ACADEMIC_YEAR]);
        ModulePreference::factory()->create([
            'module_id' => $module->module_id,
            'academic_year' => self::ACADEMIC_YEAR,
        ]);

        $ta = User::factory()->graduateTa()->create();
        $preference = TaPreference::factory()->create(['ta_email' => $ta->email, 'academic_year' => self::ACADEMIC_YEAR]);
        TaModuleChoice::factory()->create([
            'preference_id' => $preference->preference_id,
            'ta_email' => $ta->email,
            'module_id' => $module->module_id,
            'priority' => 1,
        ]);

        // Allocation history in the CURRENT year must not count as a repetition.
        Allocation::factory()->create([
            'academic_year' => self::ACADEMIC_YEAR,
            'allocation_id' => self::ACADEMIC_YEAR.'-A-01',
            'ta_id' => $ta->email,
            'module_id' => $module->module_id,
        ]);

        app(Allocator::class)->createModuleROLs();

        $rol = ModuleRankOrderList::where('module_id', $module->module_id)->where('ta_email', $ta->email)->first();

        $this->assertSame(0.0, (float) $rol->did_before_weight);
        $this->assertSame(27.0, (float) $rol->ta_total_weight);
    }

    public function test_create_module_rols_query_count_does_not_scale_with_modules_times_tas(): void
    {
        $modules = Module::factory()->count(5)->create(['academic_year' => self::ACADEMIC_YEAR]);
        foreach ($modules as $module) {
            ModulePreference::factory()->create(['module_id' => $module->module_id, 'academic_year' => self::ACADEMIC_YEAR]);
        }

        $tas = User::factory()->graduateTa()->count(5)->create();
        foreach ($tas as $ta) {
            $preference = TaPreference::factory()->create(['ta_email' => $ta->email, 'academic_year' => self::ACADEMIC_YEAR]);

            foreach ($modules as $priority => $module) {
                TaModuleChoice::factory()->create([
                    'preference_id' => $preference->preference_id,
                    'ta_email' => $ta->email,
                    'module_id' => $module->module_id,
                    'priority' => $priority + 1,
                ]);
            }
        }

        DB::enableQueryLog();
        app(Allocator::class)->createModuleROLs();
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // 5 modules x 5 TAs = 25 (module, TA) pairs. Before batching this ran one query per pair
        // (plus more inside); a fixed, small bound proves the N+1 is gone and stays gone.
        $this->assertLessThan(15, $queryCount, "Expected a small, module/TA-count-independent number of queries, got {$queryCount}.");
    }
}
