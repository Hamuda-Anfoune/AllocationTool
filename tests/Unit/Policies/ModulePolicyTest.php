<?php

namespace Tests\Unit\Policies;

use App\Models\Module;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ModulePolicyTest extends TestCase
{
    #[DataProvider('adminOrConvenorAccountTypes')]
    public function test_view_any_allows_admins_and_convenors(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', Module::class));
    }

    #[DataProvider('taAccountTypes')]
    public function test_view_any_denies_tas(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Module::class));
    }

    #[DataProvider('adminAccountTypes')]
    public function test_create_allows_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertTrue(Gate::forUser($user)->allows('create', Module::class));
    }

    public function test_create_denies_a_convenor(): void
    {
        $convenor = User::factory()->make(['account_type_id' => '002']);

        $this->assertFalse(Gate::forUser($convenor)->allows('create', Module::class));
    }

    #[DataProvider('adminAccountTypes')]
    public function test_submit_preferences_allows_admins_for_any_module(string $accountTypeId): void
    {
        $admin = User::factory()->make(['account_type_id' => $accountTypeId, 'email' => 'admin@example.com']);
        $module = Module::make(['module_id' => 'CS101', 'convenor_email' => 'convenor@example.com']);

        $this->assertTrue(Gate::forUser($admin)->allows('submitPreferences', $module));
    }

    public function test_submit_preferences_allows_the_owning_convenor(): void
    {
        $convenor = User::factory()->make(['account_type_id' => '002', 'email' => 'convenor@example.com']);
        $module = Module::make(['module_id' => 'CS101', 'convenor_email' => 'convenor@example.com']);

        $this->assertTrue(Gate::forUser($convenor)->allows('submitPreferences', $module));
    }

    public function test_submit_preferences_denies_a_convenor_who_does_not_own_the_module_with_message(): void
    {
        $convenor = User::factory()->make(['account_type_id' => '002', 'email' => 'convenor-b@example.com']);
        $module = Module::make(['module_id' => 'CS101', 'convenor_email' => 'convenor-a@example.com']);

        $response = Gate::forUser($convenor)->inspect('submitPreferences', $module);

        $this->assertFalse($response->allowed());
        $this->assertSame("You are not authorized to modify this module's preferences.", $response->message());
    }

    #[DataProvider('taAccountTypes')]
    public function test_submit_preferences_denies_tas(string $accountTypeId): void
    {
        $ta = User::factory()->make(['account_type_id' => $accountTypeId]);
        $module = Module::make(['module_id' => 'CS101', 'convenor_email' => 'convenor@example.com']);

        $this->assertFalse(Gate::forUser($ta)->allows('submitPreferences', $module));
    }

    public function test_update_and_delete_apply_the_same_ownership_rule_as_submit_preferences(): void
    {
        $convenor = User::factory()->make(['account_type_id' => '002', 'email' => 'convenor@example.com']);
        $ownModule = Module::make(['module_id' => 'CS101', 'convenor_email' => 'convenor@example.com']);
        $othersModule = Module::make(['module_id' => 'CS102', 'convenor_email' => 'someone-else@example.com']);

        $this->assertTrue(Gate::forUser($convenor)->allows('update', $ownModule));
        $this->assertTrue(Gate::forUser($convenor)->allows('delete', $ownModule));
        $this->assertFalse(Gate::forUser($convenor)->allows('update', $othersModule));
        $this->assertFalse(Gate::forUser($convenor)->allows('delete', $othersModule));
    }

    public static function adminAccountTypes(): array
    {
        return [
            'super admin' => ['000'],
            'admin' => ['001'],
        ];
    }

    public static function adminOrConvenorAccountTypes(): array
    {
        return [
            'super admin' => ['000'],
            'admin' => ['001'],
            'convenor' => ['002'],
        ];
    }

    public static function taAccountTypes(): array
    {
        return [
            'external ta' => ['003'],
            'graduate ta' => ['004'],
        ];
    }
}
