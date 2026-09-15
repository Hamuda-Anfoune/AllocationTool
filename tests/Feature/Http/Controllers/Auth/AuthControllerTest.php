<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\UniversityUser;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
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

    public function test_forgot_password_returns_generic_message_for_unknown_email(): void
    {
        $response = $this->postJson('/api/v1/forgot-password', ['email' => 'unknown@example.com']);

        $response->assertOk();
        $response->assertJsonPath('message', 'If that email is registered, a password reset link has been sent.');
    }

    public function test_forgot_password_sends_reset_notification_for_known_email(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/forgot-password', ['email' => $user->email]);

        $response->assertOk();
        $response->assertJsonPath('message', 'If that email is registered, a password reset link has been sent.');
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_updates_password_and_revokes_existing_tokens(): void
    {
        Notification::fake();

        $user = User::factory()->create(['password' => 'password123']);
        $user->createToken('api');
        $this->assertDatabaseCount('personal_access_tokens', 1);

        $this->postJson('/api/v1/forgot-password', ['email' => $user->email]);

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $response = $this->postJson('/api/v1/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertOk();
        $this->assertTrue(Hash::check('new-password123', $user->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_reset_password_rejects_an_invalid_token(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/v1/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'new-password123',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('email');
        $this->assertTrue(Hash::check('password123', $user->fresh()->password));
    }

    public function test_reset_password_rejects_a_mismatched_password_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/v1/reset-password', [
            'token' => 'irrelevant',
            'email' => $user->email,
            'password' => 'new-password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('password');
    }
}
