<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_otp_verification_succeeds_with_correct_otp(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'verification_otp' => 4321,
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/otp-verification', [
            'email' => 'jane@example.com',
            'otp' => 4321,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertNotEmpty($response->json('data.accessToken'));

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->verification_otp);
        $this->assertEquals(2, $user->profile_step);
    }

    public function test_otp_verification_fails_with_wrong_otp(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'verification_otp' => 4321,
        ]);

        $response = $this->postJson('/api/auth/otp-verification', [
            'email' => 'jane@example.com',
            'otp' => 1111,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_otp_verification_requires_email_to_exist(): void
    {
        $response = $this->postJson('/api/auth/otp-verification', [
            'email' => 'nobody@example.com',
            'otp' => 1234,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_otp_resend_generates_new_otp_and_sends_mail(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'verification_otp' => null,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/otp-verification-resend', [
            'user_id' => $user->id,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);

        $user->refresh();
        $this->assertNotNull($user->verification_otp);
        $this->assertNull($user->email_verified_at);
        Mail::assertSent(\App\Mail\AppMail::class);
    }

    public function test_otp_resend_requires_existing_user_id(): void
    {
        $response = $this->postJson('/api/auth/otp-verification-resend', [
            'user_id' => 999999,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }
}
