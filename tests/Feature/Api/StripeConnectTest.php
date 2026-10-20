<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\InteractsWithFakeStripe;
use Tests\TestCase;

class StripeConnectTest extends TestCase
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

    public function test_connect_stripe_creates_account_and_returns_onboarding_url(): void
    {
        $stripe = $this->fakeStripeHttp();
        $stripe->queue('post', '/v1/customers', ['id' => 'cus_123', 'object' => 'customer']);
        $stripe->queue('post', '/v1/accounts', ['id' => 'acct_123', 'object' => 'account']);
        $stripe->queue('post', '/v1/account_links', [
            'object' => 'account_link',
            'url' => 'https://connect.stripe.com/setup/e/acct_123/abc',
            'expires_at' => now()->addHour()->timestamp,
        ]);

        $user = User::factory()->create(['stripe_payouts_enabled' => 0]);

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/connect-stripe');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.url', 'https://connect.stripe.com/setup/e/acct_123/abc');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'stripe_account_id' => 'acct_123',
            'stripe_customer_id' => 'cus_123',
        ]);
    }

    public function test_connect_stripe_rejects_already_connected_account(): void
    {
        $user = User::factory()->create(['stripe_payouts_enabled' => 1]);

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/connect-stripe');

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_connect_stripe_requires_authentication(): void
    {
        $this->getJson('/api/connect-stripe')->assertStatus(401);
    }
}
