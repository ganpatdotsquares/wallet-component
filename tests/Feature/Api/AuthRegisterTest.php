<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user_and_zero_balance_wallet(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
            'confirm_password' => 'Secret123!',
            'phone_number' => '9876543210',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.email', 'jane@example.com');

        $user = User::where('email', 'jane@example.com')->first();
        $this->assertNotNull($user);
        $this->assertDatabaseHas('wallets', ['user_id' => $user->id, 'balance' => 0]);
    }

    public function test_register_sets_fixed_otp_outside_production(): void
    {
        $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
            'confirm_password' => 'Secret123!',
        ])->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'verification_otp' => 1234,
        ]);
    }

    public function test_register_with_valid_referral_code_links_referrer(): void
    {
        $referrer = User::factory()->create([
            'status' => 1,
            'email_verified_at' => now(),
            'wallet_referral_code' => 'REF123',
        ]);
        Wallet::create(['user_id' => $referrer->id, 'balance' => 0]);

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
            'confirm_password' => 'Secret123!',
            'wallet_referred_by' => 'REF123',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'wallet_referred_by' => 'REF123',
        ]);
    }

    public function test_register_with_invalid_referral_code_is_rejected(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
            'confirm_password' => 'Secret123!',
            'wallet_referred_by' => 'DOES-NOT-EXIST',
        ]);

        $response->assertJsonPath('status', false);
        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_register_with_duplicate_email_returns_clean_validation_error(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
            'confirm_password' => 'Secret123!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
        $this->assertEquals(1, User::where('email', 'jane@example.com')->count());
    }

    public function test_register_requires_matching_password_confirmation(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'Secret123!',
            'confirm_password' => 'Mismatch123!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_register_rejects_non_numeric_phone_number(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe1@example.com',
            'password' => 'Password@123',
            'confirm_password' => 'Password@123',
            'phone_number' => 'dads9876543210',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
        $this->assertDatabaseMissing('users', ['email' => 'john.doe1@example.com']);
    }

    public function test_register_requires_required_fields(): void
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }
}
