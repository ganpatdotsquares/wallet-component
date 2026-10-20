<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrokenRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    public function test_auth_delete_route_is_wired_to_a_missing_controller_method(): void
    {
        $response = $this->postJson('/api/auth/delete', []);

        $response->assertStatus(500);
    }

    public function test_wallet_send_route_is_wired_to_a_missing_controller_method(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/wallet/send', []);

        $response->assertStatus(500);
    }
}
