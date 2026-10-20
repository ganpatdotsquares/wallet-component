<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletBalanceAndHistoryTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    public function test_get_balance_returns_wallet_balance(): void
    {
        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 75.50]);

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/wallet/balance');

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertEquals(75.50, $response->json('data.balance'));
    }

    public function test_get_balance_defaults_to_zero_without_wallet(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/wallet/balance');

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('data.balance'));
    }

    public function test_get_balance_requires_authentication(): void
    {
        $this->getJson('/api/wallet/balance')->assertStatus(401);
    }

    public function test_transaction_history_returns_only_own_transactions_newest_first(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $older = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 10,
            'status' => 'completed',
        ]);
        $older->created_at = now()->subDay();
        $older->save();

        $newer = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'withdraw',
            'amount' => 5,
            'status' => 'pending',
        ]);
        WalletTransaction::create([
            'user_id' => $otherUser->id,
            'type' => 'deposit',
            'amount' => 99,
            'status' => 'completed',
        ]);

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/wallet/transactions');

        $response->assertStatus(200)->assertJsonPath('status', true);
        $ids = collect($response->json('data.transactions'))->pluck('id')->all();
        $this->assertEquals([$newer->id, $older->id], $ids);
    }

    public function test_transaction_history_requires_authentication(): void
    {
        $this->getJson('/api/wallet/transactions')->assertStatus(401);
    }
}
