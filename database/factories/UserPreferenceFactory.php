<?php

namespace Database\Factories;

use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserPreferenceFactory extends Factory
{
    protected $model = UserPreference::class;

    public function definition(): array
    {
        return [
            'user_id' => $this->faker->unique()->numberBetween(1, 10),
            'preferred_sources' => [1, 2, 3],
            'preferred_categories' => [4, 5],
            'preferred_authors' => [6],
        ];
    }
}
