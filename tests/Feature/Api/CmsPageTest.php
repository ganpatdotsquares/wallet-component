<?php

namespace Tests\Feature\Api;

use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CmsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer ' . $user->createToken('test')->plainTextToken];
    }

    public function test_published_page_is_returned_by_slug(): void
    {
        $user = User::factory()->create();
        Page::create([
            'title' => 'About Us',
            'category' => 'static',
            'slug' => 'about-us',
            'description' => 'All about us.',
            'status' => 1,
        ]);

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/page/about-us');

        $response->assertStatus(200)
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.slug', 'about-us');
    }

    public function test_unpublished_page_is_not_returned(): void
    {
        $user = User::factory()->create();
        Page::create([
            'title' => 'Draft Page',
            'category' => 'static',
            'slug' => 'draft-page',
            'description' => 'Not yet live.',
            'status' => 0,
        ]);

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/page/draft-page');

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_unknown_slug_returns_not_found_error(): void
    {
        $user = User::factory()->create();

        $response = $this->withHeaders($this->authHeaders($user))->getJson('/api/page/does-not-exist');

        $response->assertStatus(200)->assertJsonPath('status', false);
    }

    public function test_cms_page_requires_authentication(): void
    {
        $this->getJson('/api/page/about-us')->assertStatus(401);
    }
}
