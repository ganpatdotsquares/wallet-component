<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Secret123!'),
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.token_type', 'Bearer');
        $this->assertNotEmpty($response->json('data.access_token'));
    }

    public function test_login_fails_for_unregistered_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nobody@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Secret123!'),
            'status' => 1,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_login_fails_for_deactivated_account(): void
    {
        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Secret123!'),
            'status' => 0,
            'email_verified_at' => now(),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_login_with_unverified_email_sends_new_otp_and_blocks_login(): void
    {
        Mail::fake();

        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Secret123!'),
            'status' => 1,
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
        Mail::assertSent(\App\Mail\AppMail::class);
    }

    public function test_login_with_unverified_email_still_responds_when_mail_delivery_fails(): void
    {
        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->andThrow(new \Symfony\Component\Mailer\Exception\TransportException(
            'Expected response code "250/251/252" but got code "550", with message "550 mailbox unavailable".'
        ));

        User::factory()->create([
            'email' => 'jane@example.com',
            'password' => Hash::make('Secret123!'),
            'status' => 1,
            'email_verified_at' => null,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_login_requires_email_and_password(): void
    {
        $response = $this->postJson('/api/auth/login', []);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }
}
