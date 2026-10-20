<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WithdrawRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletWithdrawTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    protected function validPayload(): array
    {
        return [
            'amount' => 100,
            'method' => 'bank',
            'method_details' => [
                'account_number' => '123456789',
                'ifsc' => 'ABCD0123456',
                'account_holder_name' => 'Jane Doe',
                'bank_name' => 'Test Bank',
            ],
        ];
    }

    public function test_withdraw_request_succeeds_with_sufficient_balance_and_stripe_account(): void
    {
        $user = User::factory()->create(['stripe_account_id' => 'acct_123']);
        Wallet::create(['user_id' => $user->id, 'balance' => 200]);

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request', $this->validPayload());

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertDatabaseHas('withdraw_requests', [
            'user_id' => $user->id,
            'amount' => 100,
            'status' => 'pending',
        ]);
    }

    public function test_withdraw_request_fails_with_insufficient_balance(): void
    {
        $user = User::factory()->create(['stripe_account_id' => 'acct_123']);
        Wallet::create(['user_id' => $user->id, 'balance' => 10]);

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request', $this->validPayload());

        $response->assertJsonPath('status', false);
        $this->assertDatabaseMissing('withdraw_requests', ['user_id' => $user->id]);
    }

    public function test_withdraw_request_fails_without_connected_stripe_account(): void
    {
        $user = User::factory()->create(['stripe_account_id' => null]);
        Wallet::create(['user_id' => $user->id, 'balance' => 200]);

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request', $this->validPayload());

        $response->assertJsonPath('status', false);
    }

    public function test_withdraw_request_fails_when_pending_request_already_exists(): void
    {
        $user = User::factory()->create(['stripe_account_id' => 'acct_123']);
        Wallet::create(['user_id' => $user->id, 'balance' => 200]);
        WithdrawRequest::create([
            'user_id' => $user->id,
            'amount' => 50,
            'method' => 'bank',
            'method_details' => json_encode(['x' => 'y']),
            'status' => 'pending',
        ]);

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request', $this->validPayload());

        $response->assertJsonPath('status', false);
        $this->assertEquals(1, WithdrawRequest::where('user_id', $user->id)->count());
    }

    public function test_withdraw_request_rejects_non_numeric_account_number(): void
    {
        $user = User::factory()->create(['stripe_account_id' => 'acct_123']);
        Wallet::create(['user_id' => $user->id, 'balance' => 200]);

        $payload = $this->validPayload();
        $payload['method_details']['account_number'] = 'abcd56789';

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request', $payload);

        $response->assertStatus(200)->assertJsonPath('status', false);
        $this->assertDatabaseMissing('withdraw_requests', ['user_id' => $user->id]);
    }

    public function test_withdraw_request_rejects_malformed_ifsc(): void
    {
        $user = User::factory()->create(['stripe_account_id' => 'acct_123']);
        Wallet::create(['user_id' => $user->id, 'balance' => 200]);

        $payload = $this->validPayload();
        $payload['method_details']['ifsc'] = 'NOTANIFSC12';

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request', $payload);

        $response->assertStatus(200)->assertJsonPath('status', false);
        $this->assertDatabaseMissing('withdraw_requests', ['user_id' => $user->id]);
    }

    public function test_withdraw_request_validates_method_details(): void
    {
        $user = User::factory()->create(['stripe_account_id' => 'acct_123']);
        Wallet::create(['user_id' => $user->id, 'balance' => 200]);

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/withdraw-request', [
            'amount' => 100,
            'method' => 'bank',
            'method_details' => ['account_number' => '123'],
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_withdraw_request_requires_authentication(): void
    {
        $this->postJson('/api/wallet/withdraw-request', $this->validPayload())->assertStatus(401);
    }

    public function test_withdraw_request_cancel_marks_pending_request_cancelled(): void
    {
        $user = User::factory()->create();
        $withdraw = WithdrawRequest::create([
            'user_id' => $user->id,
            'amount' => 50,
            'method' => 'bank',
            'method_details' => json_encode(['x' => 'y']),
            'status' => 'pending',
        ]);

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request-cancel');

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertDatabaseHas('withdraw_requests', ['id' => $withdraw->id, 'status' => 'cancelled']);
    }

    public function test_withdraw_request_cancel_fails_without_pending_request(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))
            ->postJson('/api/wallet/withdraw-request-cancel');

        $response->assertJsonPath('status', false);
    }

    public function test_withdraw_request_cancel_requires_authentication(): void
    {
        $this->postJson('/api/wallet/withdraw-request-cancel')->assertStatus(401);
    }
}
