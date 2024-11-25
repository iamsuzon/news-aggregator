<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserPreferenceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_creates_or_updates_user_preferences_successfully()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');
        $payload = [
            'preferred_sources' => [1, 2, 3],
            'preferred_categories' => [4, 5],
            'preferred_authors' => [6],
        ];
        $response = $this->PostJson('/api/v1/user/preferences', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Preferences updated successfully',
                'preferences' => [
                    'user_id' => $user->id,
                    'preferred_sources' => [1, 2, 3],
                    'preferred_categories' => [4, 5],
                    'preferred_authors' => [6],
                ],
            ]);
    }

    public function test_returns_validation_errors_for_invalid_payload()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');
        $payload = [
            'preferred_sources' => ['not_an_integer'],
            'preferred_categories' => 'not_an_array',
        ];
        $response = $this->PostJson('/api/v1/user/preferences', $payload);

        $response->assertStatus(422)
            ->assertJsonStructure(['errors' => [
                'preferred_sources.0',
                'preferred_categories',
            ]]);
    }

    public function test_returns_unauthorized_when_user_is_not_authenticated()
    {
        $payload = [
            'preferred_sources' => [1, 2, 3],
            'preferred_categories' => [4, 5],
            'preferred_authors' => [6],
        ];

        $response = $this->PostJson('/api/v1/user/preferences', $payload);
        $response->assertStatus(401);
    }

    public function test_updates_existing_preferences_for_authenticated_user()
    {
        $user = User::factory()->create();
        UserPreference::factory()->create([
            'user_id' => $user->id,
            'preferred_sources' => [1],
            'preferred_categories' => [2],
            'preferred_authors' => [3],
        ]);

        $this->actingAs($user, 'sanctum');
        $payload = [
            'preferred_sources' => [4, 5],
            'preferred_categories' => [6],
            'preferred_authors' => [7, 8],
        ];
        $response = $this->PostJson('/api/v1/user/preferences', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Preferences updated successfully',
                'preferences' => [
                    'user_id' => $user->id,
                    'preferred_sources' => [4, 5],
                    'preferred_categories' => [6],
                    'preferred_authors' => [7, 8],
                ],
            ]);
    }
}
