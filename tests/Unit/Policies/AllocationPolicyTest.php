<?php

namespace Tests\Unit\Policies;

use App\Models\Allocation;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AllocationPolicyTest extends TestCase
{
    #[DataProvider('adminAccountTypes')]
    public function test_view_any_create_and_delete_allow_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertTrue(Gate::forUser($user)->allows('viewAny', Allocation::class));
        $this->assertTrue(Gate::forUser($user)->allows('create', Allocation::class));
        $this->assertTrue(Gate::forUser($user)->allows('delete', Allocation::class));
    }

    #[DataProvider('nonAdminAccountTypes')]
    public function test_view_any_create_and_delete_deny_non_admins(string $accountTypeId): void
    {
        $user = User::factory()->make(['account_type_id' => $accountTypeId]);

        $this->assertFalse(Gate::forUser($user)->allows('viewAny', Allocation::class));
        $this->assertFalse(Gate::forUser($user)->allows('create', Allocation::class));
        $this->assertFalse(Gate::forUser($user)->allows('delete', Allocation::class));
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
