<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_sends_otp_for_active_user(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'status' => 1,
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'jane@example.com',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);
        $user->refresh();
        $this->assertNotNull($user->forgot_password_otp);
        Mail::assertSent(\App\Mail\AppMail::class);
    }

    public function test_forgot_password_blocks_deactivated_user(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'status' => 0,
        ]);

        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'jane@example.com',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_forgot_password_requires_existing_email(): void
    {
        $response = $this->postJson('/api/auth/forgot-password', [
            'email' => 'nobody@example.com',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_forgot_password_otp_issues_token_on_match(): void
    {
        $user = User::factory()->create([
            'forgot_password_otp' => 5566,
        ]);

        $response = $this->postJson('/api/auth/forgot-password-otp', [
            'email' => $user->email,
            'otp' => 5566,
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertNotEmpty($response->json('data.accessToken'));

        $user->refresh();
        $this->assertNull($user->forgot_password_otp);
    }

    public function test_forgot_password_otp_fails_on_mismatch(): void
    {
        $user = User::factory()->create([
            'forgot_password_otp' => 5566,
        ]);

        $response = $this->postJson('/api/auth/forgot-password-otp', [
            'email' => $user->email,
            'otp' => 1111,
            'user_id' => $user->id,
        ]);

        $response->assertStatus(404)->assertJsonPath('status', false);
    }

    public function test_reset_password_updates_password_and_revokes_tokens(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword1!'),
        ]);
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->postJson('/api/auth/reset-password', [
                'user_id' => $user->id,
                'password' => 'NewPassword1!',
                'c_password' => 'NewPassword1!',
            ]);

        $response->assertStatus(200)->assertJsonPath('status', true);

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword1!', $user->password));
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_reset_password_rejects_same_password_as_current(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('OldPassword1!'),
        ]);

        $response = $this->postJson('/api/auth/reset-password', [
            'user_id' => $user->id,
            'password' => 'OldPassword1!',
            'c_password' => 'OldPassword1!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
        $user->refresh();
        $this->assertTrue(Hash::check('OldPassword1!', $user->password));
    }

    public function test_reset_password_requires_matching_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/reset-password', [
            'user_id' => $user->id,
            'password' => 'NewPassword1!',
            'c_password' => 'Mismatch1!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_reset_password_requires_complex_password(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/auth/reset-password', [
            'user_id' => $user->id,
            'password' => 'weak',
            'c_password' => 'weak',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_logout_revokes_tokens(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token])
            ->getJson('/api/logout');

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_logout_requires_authentication(): void
    {
        $response = $this->getJson('/api/logout');

        $response->assertStatus(401);
    }
}
