<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\UniversityUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_a_user_for_an_existing_university_user_and_returns_a_token(): void
    {
        UniversityUser::create(['email' => 'ta@example.com', 'account_type_id' => '004']);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test TA',
            'email' => 'ta@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('user.email', 'ta@example.com');
        $response->assertJsonPath('user.account_type_id', '004');
        $this->assertIsString($response->json('token'));
        $this->assertDatabaseHas('users', ['email' => 'ta@example.com', 'account_type_id' => '004']);
    }

    public function test_register_rejects_an_email_with_no_matching_university_user(): void
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Nobody',
            'email' => 'unknown@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'unknown@example.com']);
    }

    public function test_register_rejects_an_email_already_registered(): void
    {
        $existing = User::factory()->create();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Duplicate',
            'email' => $existing->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function test_register_rejects_a_university_user_with_no_valid_account_type(): void
    {
        UniversityUser::create(['email' => 'ta@example.com', 'account_type_id' => '999']);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test TA',
            'email' => 'ta@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'ta@example.com']);
    }

    public function test_login_returns_a_token_for_valid_credentials(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk();
        $response->assertJsonPath('user.email', $user->email);
        $this->assertIsString($response->json('token'));
    }

    public function test_login_rejects_an_incorrect_password(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_rejects_a_deactivated_account(): void
    {
        $user = User::factory()->create(['password' => 'password123', 'active' => false]);

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function test_login_is_rate_limited_after_repeated_failures(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/v1/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
    }

    public function test_logout_revokes_the_current_token(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $login = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $token = $login->json('token');
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $response = $this->withToken($token)->postJson('/api/v1/logout');

        $response->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->postJson('/api/v1/logout')->assertUnauthorized();
    }
}
