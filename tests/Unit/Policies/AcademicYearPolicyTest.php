<?php

namespace Tests\Unit\Policies;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AcademicYearPolicyTest extends TestCase
{
    #[DataProvider('adminAccountTypes')]
    public function test_update_allows_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertTrue(Gate::forUser($user)->allows('update', AcademicYear::class));
    }

    #[DataProvider('nonAdminAccountTypes')]
    public function test_update_denies_non_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertFalse(Gate::forUser($user)->allows('update', AcademicYear::class));
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
