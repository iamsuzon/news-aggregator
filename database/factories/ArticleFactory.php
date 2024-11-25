<?php

namespace Database\Factories;

use App\Enums\PostVisibilityEnum;
use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'description' => $this->faker->paragraph,
            'content' => $this->faker->paragraph,
            'url' => $this->faker->url,
            'image_url' => $this->faker->imageUrl(),
            'published_at' => $this->faker->dateTime,
            'status' => true,
            'visibility' => PostVisibilityEnum::Public,
            'type' => 'article',
            'scraped_source' => $this->faker->url,
            'scraped_index' => 1,
        ];
    }
}
