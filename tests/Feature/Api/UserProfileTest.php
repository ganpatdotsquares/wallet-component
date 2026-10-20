<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    public function test_my_profile_returns_authenticated_user(): void
    {
        $user = User::factory()->create(['first_name' => 'Jane']);

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/my-profile');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.first_name', 'Jane');
    }

    public function test_my_profile_requires_authentication(): void
    {
        $this->getJson('/api/my-profile')->assertStatus(401);
    }

    public function test_my_profile_rejects_invalid_token(): void
    {
        $this->withHeaders(['Authorization' => 'Bearer invalid-token'])
            ->getJson('/api/my-profile')
            ->assertStatus(401);
    }

    public function test_deactivated_user_is_rejected_by_protected_routes(): void
    {
        $user = User::factory()->create(['status' => 1]);
        $headers = $this->authHeaders($user);
        $user->update(['status' => 0]);

        $this->withHeaders($headers)->getJson('/api/my-profile')->assertStatus(401);
    }

    public function test_update_profile_updates_basic_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/update-profile', [
            'first_name' => 'Updated',
            'last_name' => 'Name',
            'phone_number' => '1234567890',
            'gender' => 'male',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
            'last_name' => 'Name',
        ]);
    }

    public function test_update_profile_rejects_non_numeric_phone_number(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/update-profile', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'phone_number' => 'dads9876543210',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_update_profile_requires_first_and_last_name(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/update-profile', []);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_update_profile_uploads_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->post('/api/update-profile', [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'profile_image' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);
        $user->refresh();
        $this->assertNotNull($user->profile_image);
        Storage::disk('public')->assertExists($user->profile_image);
    }

    public function test_change_password_succeeds_with_valid_new_password(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword1!')]);

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/change-password', [
            'current_password' => 'OldPassword1!',
            'password' => 'NewPassword1!',
            'c_password' => 'NewPassword1!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', true);
        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword1!', $user->password));
    }

    public function test_change_password_rejects_same_password_as_current(): void
    {
        $user = User::factory()->create(['password' => Hash::make('OldPassword1!')]);

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/change-password', [
            'current_password' => 'OldPassword1!',
            'password' => 'OldPassword1!',
            'c_password' => 'OldPassword1!',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_change_password_requires_complexity_and_confirmation(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->postJson('/api/change-password', [
            'current_password' => 'whatever',
            'password' => 'weak',
            'c_password' => 'different',
        ]);

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_delete_account_soft_deletes_user_and_revokes_token(): void
    {
        $user = User::factory()->create();
        $headers = $this->authHeaders($user);

        $response = $this->withHeaders($headers)->getJson('/api/delete-account');

        $response->assertStatus(200)->assertJsonPath('status', true);
        $this->assertSoftDeleted('users', ['id' => $user->id]);
        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_delete_account_requires_authentication(): void
    {
        $this->getJson('/api/delete-account')->assertStatus(401);
    }
}
