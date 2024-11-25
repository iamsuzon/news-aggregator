<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserNewsFeedApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_returns_error_when_no_preferences_are_set()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/v1/user/articles');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'No preferences found. Please set your preferences first.',
            ]);
    }

    public function test_returns_articles_based_on_user_preferences()
    {
        $user = User::factory()->create();
        $preferences = UserPreference::factory()->create([
            'user_id' => $user->id,
            'preferred_sources' => [1, 2],
            'preferred_categories' => [3],
            'preferred_authors' => [4],
        ]);

        $matchingArticle = Article::factory()->create([
            'source_id' => 1,
            'category_id' => 3,
            'author_id' => 4,
        ]);
        $nonMatchingArticle = Article::factory()->create([
            'source_id' => 99,
            'category_id' => 99,
            'author_id' => 99,
        ]);

        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/v1/user/articles');

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $matchingArticle->id])
            ->assertJsonMissing(['id' => $nonMatchingArticle->id]);
    }

    public function test_returns_paginated_feed()
    {
        $user = User::factory()->create();
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'preferred_sources' => [1],
        ]);

        Article::factory()->count(15)->create([
            'source_id' => 1,
        ]);

        $this->actingAs($user, 'sanctum');
        $response = $this->getJson('/api/v1/user/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'source', 'category', 'author', 'published_at'],
                ],
                'pagination' => [
                    'current_page',
                    'per_page',
                    'total',
                    'last_page',
                ],
            ]);
    }

    public function test_returns_unauthorized_for_unauthenticated_access()
    {
        $response = $this->getJson('/api/v1/user/articles');

        $response->assertStatus(401);
    }
}
