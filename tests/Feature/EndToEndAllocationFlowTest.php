<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Drives the full user journey through real HTTP calls, end to end:
 * bootstrap a university -> register/login a convenor and a TA -> submit module
 * and TA preferences -> run an allocation -> fetch the results. Every actor's
 * bearer token comes from a real register/login response, not Sanctum::actingAs,
 * so this exercises the same request path a real API client would use.
 */
class EndToEndAllocationFlowTest extends TestCase
{
    use RefreshDatabase;

    private const ACADEMIC_YEAR = '2020-2021-01';

    public function test_the_full_allocation_journey_succeeds_end_to_end(): void
    {
        $adminToken = $this->bootstrapUniversityAndGetAdminToken();

        $this->as($adminToken)->postJson('/api/v1/admin/university-users', [
            'email' => 'convenor@example.com',
            'account_type_id' => '002',
        ])->assertCreated();

        $this->as($adminToken)->postJson('/api/v1/admin/university-users', [
            'email' => 'ta@example.com',
            'account_type_id' => '004',
        ])->assertCreated();

        $convenorToken = $this->registerAndLogin('Test Convenor', 'convenor@example.com');
        $taToken = $this->registerAndLogin('Test TA', 'ta@example.com');

        $this->as($adminToken)->postJson('/api/v1/admin/modules', [
            'module_id' => 'CS101',
            'module_name' => 'Intro to CS',
            'academic_year' => self::ACADEMIC_YEAR,
            'convenor_email' => 'convenor@example.com',
        ])->assertCreated();

        $this->as($convenorToken)->postJson('/api/v1/module-preferences', [
            'module_id' => 'CS101',
            'academic_year' => self::ACADEMIC_YEAR,
            'no_of_assistants' => 1,
            'no_of_contact_hours' => 4,
            'no_of_marking_hours' => 6,
        ])->assertCreated();

        $this->as($taToken)->postJson('/api/v1/ta-preferences', [
            'module_1_id' => 'CS101',
            'max_modules' => 2,
            'max_contact_hours' => 10,
            'max_marking_hours' => 10,
            'academic_year' => self::ACADEMIC_YEAR,
        ])->assertCreated();

        $allocateResponse = $this->as($adminToken)->postJson('/api/v1/admin/allocations');
        $allocateResponse->assertCreated();
        $allocationId = $allocateResponse->json('allocation_id');
        $this->assertSame(self::ACADEMIC_YEAR.'-A-01', $allocationId);

        $indexResponse = $this->as($adminToken)->getJson('/api/v1/admin/allocations');
        $indexResponse->assertOk();
        $this->assertContains($allocationId, collect($indexResponse->json('allocations'))->pluck('allocation_id')->all());

        $showResponse = $this->as($adminToken)->getJson("/api/v1/admin/allocations/{$allocationId}");
        $showResponse->assertOk();
        $showResponse->assertJsonPath('allocation_data.CS101.0.module_id', 'CS101');
        $showResponse->assertJsonPath('allocation_data.CS101.0.ta_id', 'ta@example.com');
        $showResponse->assertJsonPath('ta_allocation_data.0.ta_id', 'ta@example.com');
        $showResponse->assertJsonPath('ta_allocation_data.0.contact_hours', 4);
        $showResponse->assertJsonPath('ta_allocation_data.0.marking_hours', 6);

        $this->assertDatabaseHas('allocations', [
            'allocation_id' => $allocationId,
            'module_id' => 'CS101',
            'ta_id' => 'ta@example.com',
        ]);
    }

    private function bootstrapUniversityAndGetAdminToken(): string
    {
        $response = $this->postJson('/api/v1/admin/universities', [
            'university_name' => 'Test University',
            'university_email' => 'contact@test-university.edu',
            'user_name' => 'First Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated();

        return $response->json('token');
    }

    private function registerAndLogin(string $name, string $email): string
    {
        $registerResponse = $this->postJson('/api/v1/register', [
            'name' => $name,
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $registerResponse->assertCreated();

        $loginResponse = $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => 'password123',
        ]);
        $loginResponse->assertOk();

        return $loginResponse->json('token');
    }

    /**
     * Authenticate the next request as the bearer of this token.
     *
     * Sanctum's guard is a RequestGuard, which caches the resolved user for its
     * lifetime (see Illuminate\Auth\RequestGuard::user()). Because Laravel's HTTP
     * test helpers reuse the same application/guard instances across every
     * simulated request within one test method, switching actors by bearer token
     * alone would keep re-authenticating as whichever user was resolved first.
     * Forgetting the cached guards forces a fresh lookup against the new token.
     */
    private function as(string $token): static
    {
        Auth::forgetGuards();

        return $this->withToken($token);
    }
}
