<?php

namespace Tests\Unit\Policies;

use App\Models\UniversityUser;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UniversityUserPolicyTest extends TestCase
{
    #[DataProvider('adminAccountTypes')]
    public function test_view_any_and_create_allow_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', UniversityUser::class));
        $this->assertTrue(Gate::forUser($user)->allows('create', UniversityUser::class));
    }

    #[DataProvider('nonAdminAccountTypes')]
    public function test_view_any_and_create_deny_non_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertFalse(Gate::forUser($user)->allows('viewAny', UniversityUser::class));
        $this->assertFalse(Gate::forUser($user)->allows('create', UniversityUser::class));
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
