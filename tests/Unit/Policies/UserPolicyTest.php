<?php

namespace Tests\Unit\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserPolicyTest extends TestCase
{
    #[DataProvider('adminAccountTypes')]
    public function test_view_any_allows_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', User::class));
    }

    #[DataProvider('nonAdminAccountTypes')]
    public function test_view_any_denies_non_admins_with_message(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $response = Gate::forUser($user)->inspect('viewAny', User::class);

        $this->assertFalse($response->allowed());
        $this->assertSame('Sorry, only admins can view this information.', $response->message());
    }

    public function test_view_admins_allows_only_the_super_admin(): void
    {
        $superAdmin = User::factory()->make(['account_type_id' => '000']);

        $this->assertTrue(Gate::forUser($superAdmin)->allows('viewAdmins', User::class));
    }

    public function test_view_admins_denies_a_regular_admin_with_message(): void
    {
        $admin = User::factory()->make(['account_type_id' => '001']);

        $response = Gate::forUser($admin)->inspect('viewAdmins', User::class);

        $this->assertFalse($response->allowed());
        $this->assertSame('Sorry, only super admins can view this information.', $response->message());
    }

    #[DataProvider('adminAccountTypes')]
    public function test_view_ta_preferences_allows_admins_for_any_target(string $accountTypeId): void
    {
        $admin = User::factory()->make(['account_type_id' => $accountTypeId, 'email' => 'admin@example.com']);
        $target = User::factory()->make(['account_type_id' => '003', 'email' => 'ta@example.com']);

        $this->assertTrue(Gate::forUser($admin)->allows('viewTaPreferences', $target));
    }

    public function test_view_ta_preferences_allows_a_ta_to_view_their_own(): void
    {
        $ta = User::factory()->make(['account_type_id' => '003', 'email' => 'ta@example.com']);

        $this->assertTrue(Gate::forUser($ta)->allows('viewTaPreferences', $ta));
    }

    public function test_view_ta_preferences_denies_a_ta_viewing_another_ta_with_message(): void
    {
        $ta = User::factory()->make(['account_type_id' => '003', 'email' => 'ta@example.com']);
        $otherTa = User::factory()->make(['account_type_id' => '003', 'email' => 'other-ta@example.com']);

        $response = Gate::forUser($ta)->inspect('viewTaPreferences', $otherTa);

        $this->assertFalse($response->allowed());
        $this->assertSame('Sorry, only admins and teaching assistants can view this information.', $response->message());
    }

    public function test_view_ta_preferences_denies_a_convenor(): void
    {
        $convenor = User::factory()->make(['account_type_id' => '002', 'email' => 'conv@example.com']);
        $target = User::factory()->make(['account_type_id' => '003', 'email' => 'ta@example.com']);

        $this->assertFalse(Gate::forUser($convenor)->allows('viewTaPreferences', $target));
    }

    #[DataProvider('adminAccountTypes')]
    public function test_delete_allows_admins(string $accountTypeId): void
    {
        $admin = User::factory()->make(['account_type_id' => $accountTypeId]);
        $target = User::factory()->make(['account_type_id' => '003']);

        $this->assertTrue(Gate::forUser($admin)->allows('delete', $target));
    }

    #[DataProvider('nonAdminAccountTypes')]
    public function test_delete_denies_non_admins_with_message(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);
        $target = User::factory()->make(['account_type_id' => '003']);

        $response = Gate::forUser($user)->inspect('delete', $target);

        $this->assertFalse($response->allowed());
        $this->assertSame('Sorry, only admins can delete users.', $response->message());
    }

    public static function adminAccountTypes(): array
    {
        return [
            'super admin' => ['000'],
            'admin' => ['001'],
        ];
    }

    public static function nonAdminAccountTypes(): array
    {
        return [
            'convenor' => ['002'],
            'external ta' => ['003'],
            'graduate ta' => ['004'],
        ];
    }
}
