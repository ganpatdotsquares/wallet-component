<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithFakeStripe;
use Tests\TestCase;

class WalletDepositTest extends TestCase
{
    use RefreshDatabase;
    use InteractsWithFakeStripe;

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    protected function tearDown(): void
    {
        $this->tearDownFakeStripe();
        parent::tearDown();
    }

    public function test_deposit_initiate_creates_stripe_customer_and_payment_intent(): void
    {
        $stripe = $this->fakeStripeHttp();
        $stripe->queue('post', '/v1/customers', ['id' => 'cus_123', 'object' => 'customer']);
        $stripe->queue('post', '/v1/payment_intents', [
            'id' => 'pi_123',
            'object' => 'payment_intent',
            'client_secret' => 'pi_123_secret_abc',
            'status' => 'requires_payment_method',
            'amount' => 5000,
            'currency' => 'usd',
        ]);

        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/deposit/initiate', [
            'amount' => 50,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.payment_intent_id', 'pi_123')
            ->assertJsonPath('data.client_secret', 'pi_123_secret_abc');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'stripe_customer_id' => 'cus_123']);
    }

    public function test_deposit_initiate_reuses_existing_stripe_customer(): void
    {
        $stripe = $this->fakeStripeHttp();
        $stripe->queue('post', '/v1/payment_intents', [
            'id' => 'pi_124',
            'object' => 'payment_intent',
            'client_secret' => 'pi_124_secret_abc',
            'status' => 'requires_payment_method',
            'amount' => 1000,
            'currency' => 'usd',
        ]);

        $user = User::factory()->create(['stripe_customer_id' => 'cus_existing']);

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/deposit/initiate', [
            'amount' => 10,
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertCount(1, $stripe->requests);
    }

    public function test_deposit_initiate_requires_valid_amount(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/deposit/initiate', [
            'amount' => 0,
        ]);

        $response->assertStatus(422);
    }

    public function test_deposit_initiate_requires_authentication(): void
    {
        $this->postJson('/api/wallet/deposit/initiate', ['amount' => 10])->assertStatus(401);
    }

    public function test_deposit_confirm_credits_wallet_on_succeeded_payment(): void
    {
        $stripe = $this->fakeStripeHttp();
        $stripe->queue('get', '/v1/payment_intents/pi_123', [
            'id' => 'pi_123',
            'object' => 'payment_intent',
            'status' => 'succeeded',
            'amount' => 5000,
            'currency' => 'usd',
        ]);

        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 10]);

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/deposit/confirm', [
            'payment_intent_id' => 'pi_123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.balance', 60);

        $this->assertDatabaseHas('wallets', ['user_id' => $user->id, 'balance' => 60]);
        $this->assertDatabaseHas('wallet_transactions', [
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => 50,
            'status' => 'completed',
            'transaction_id' => 'pi_123',
        ]);
    }

    public function test_deposit_confirm_rejects_unsucceeded_payment(): void
    {
        $stripe = $this->fakeStripeHttp();
        $stripe->queue('get', '/v1/payment_intents/pi_999', [
            'id' => 'pi_999',
            'object' => 'payment_intent',
            'status' => 'requires_action',
            'amount' => 5000,
            'currency' => 'usd',
        ]);

        $user = User::factory()->create();
        Wallet::create(['user_id' => $user->id, 'balance' => 10]);

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/deposit/confirm', [
            'payment_intent_id' => 'pi_999',
        ]);

        $response->assertStatus(404)->assertJsonPath('status', false);
        $this->assertDatabaseHas('wallets', ['user_id' => $user->id, 'balance' => 10]);
    }

    public function test_deposit_confirm_requires_payment_intent_id(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/deposit/confirm', []);

        $response->assertStatus(422);
    }
}
