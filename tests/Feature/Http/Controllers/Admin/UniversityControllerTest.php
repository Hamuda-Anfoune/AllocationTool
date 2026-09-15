<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniversityControllerTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'university_name' => 'Test University',
            'university_email' => 'contact@test-university.edu',
            'user_name' => 'First Admin',
            'email' => 'admin@test-university.edu',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test_store_bootstraps_a_university_and_its_first_super_admin(): void
    {
        $response = $this->postJson('/api/v1/admin/universities', $this->payload());

        $response->assertCreated();
        $response->assertJsonPath('user.email', 'admin@test-university.edu');
        $response->assertJsonPath('user.account_type_id', '000');
        $this->assertIsString($response->json('token'));

        $this->assertDatabaseHas('universities', ['university_email' => 'contact@test-university.edu']);
        $this->assertDatabaseHas('university_users', ['email' => 'admin@test-university.edu', 'account_type_id' => '000']);
        $this->assertDatabaseHas('users', ['email' => 'admin@test-university.edu', 'account_type_id' => '000']);
    }

    public function test_store_rejects_a_duplicate_university_email(): void
    {
        $this->postJson('/api/v1/admin/universities', $this->payload())->assertCreated();

        $response = $this->postJson('/api/v1/admin/universities', $this->payload([
            'email' => 'someone-else@test-university.edu',
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('university_email');
    }

    public function test_store_rejects_a_duplicate_admin_email(): void
    {
        $this->postJson('/api/v1/admin/universities', $this->payload())->assertCreated();

        $response = $this->postJson('/api/v1/admin/universities', $this->payload([
            'university_email' => 'contact@another-university.edu',
        ]));

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }
}
