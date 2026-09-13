<?php

namespace Tests\Unit\Policies;

use App\Models\TaPreference;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class TaPreferencePolicyTest extends TestCase
{
    #[DataProvider('taAccountTypes')]
    public function test_create_allows_ta_and_gta(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertTrue(Gate::forUser($user)->allows('create', TaPreference::class));
    }

    #[DataProvider('nonTaAccountTypes')]
    public function test_create_denies_non_ta_account_types(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertFalse(Gate::forUser($user)->allows('create', TaPreference::class));
    }

    #[DataProvider('adminAccountTypes')]
    public function test_view_allows_admin_for_any_preference(string $accountTypeId): void
    {
        $admin = User::factory()->make(['account_type_id' => $accountTypeId, 'email' => 'admin@example.com']);
        $preference = TaPreference::make(['preference_id' => 'p1', 'ta_email' => 'ta@example.com']);

        $this->assertTrue(Gate::forUser($admin)->allows('view', $preference));
    }

    #[DataProvider('taAccountTypes')]
    public function test_view_allows_the_owning_ta(string $accountTypeId): void
    {
        $ta = User::factory()->make(['account_type_id' => $accountTypeId, 'email' => 'ta@example.com']);
        $preference = TaPreference::make(['preference_id' => 'p1', 'ta_email' => 'ta@example.com']);

        $this->assertTrue(Gate::forUser($ta)->allows('view', $preference));
    }

    public function test_view_denies_a_ta_who_does_not_own_the_preference_with_message(): void
    {
        $ta = User::factory()->make(['account_type_id' => '003', 'email' => 'ta@example.com']);
        $preference = TaPreference::make(['preference_id' => 'p1', 'ta_email' => 'someone-else@example.com']);

        $response = Gate::forUser($ta)->inspect('view', $preference);

        $this->assertFalse($response->allowed());
        $this->assertSame('You are not authorized to access this preference.', $response->message());
    }

    public function test_view_denies_a_convenor_even_for_their_own_email(): void
    {
        $convenor = User::factory()->make(['account_type_id' => '002', 'email' => 'conv@example.com']);
        $preference = TaPreference::make(['preference_id' => 'p1', 'ta_email' => 'conv@example.com']);

        $this->assertFalse(Gate::forUser($convenor)->allows('view', $preference));
    }

    public function test_update_and_delete_apply_the_same_ownership_rule_as_view(): void
    {
        $ta = User::factory()->make(['account_type_id' => '003', 'email' => 'ta@example.com']);
        $ownPreference = TaPreference::make(['preference_id' => 'p1', 'ta_email' => 'ta@example.com']);
        $othersPreference = TaPreference::make(['preference_id' => 'p2', 'ta_email' => 'other@example.com']);

        $this->assertTrue(Gate::forUser($ta)->allows('update', $ownPreference));
        $this->assertTrue(Gate::forUser($ta)->allows('delete', $ownPreference));
        $this->assertFalse(Gate::forUser($ta)->allows('update', $othersPreference));
        $this->assertFalse(Gate::forUser($ta)->allows('delete', $othersPreference));
    }

    public static function adminAccountTypes(): array
    {
        return [
            'super admin' => ['000'],
            'admin' => ['001'],
        ];
    }

    public static function taAccountTypes(): array
    {
        return [
            'external ta' => ['003'],
            'graduate ta' => ['004'],
        ];
    }

    public static function nonTaAccountTypes(): array
    {
        return [
            'super admin' => ['000'],
            'admin' => ['001'],
            'convenor' => ['002'],
        ];
    }
}
