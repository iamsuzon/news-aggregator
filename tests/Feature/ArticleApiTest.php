<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ArticleApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_article_retrieval(): void
    {
        $category = Category::factory()->create();
        $source = Source::factory()->create();
        $author = Author::factory()->create();

        Article::factory(15)->create([
            'status' => true,
            'category_id' => $category->id,
            'source_id' => $source->id,
            'author_id' => $author->id,
        ]);

        $response = $this->json('GET', '/api/v1/articles');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'category', 'source', 'author', 'published_at'],
                ],
                'pagination' => ['current_page', 'per_page', 'total', 'last_page'],
            ])
            ->assertJsonFragment(['current_page' => 1]);
    }

    public function test_filters_articles_by_keyword()
    {
        Article::factory()->create(['title' => 'News Testing', 'status' => true]);
        Article::factory()->create(['title' => 'Unrelated Article', 'status' => true]);

        $response = $this->json('GET', '/api/v1/articles', ['keyword' => 'News']);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'News Testing']);
    }

    public function test_filters_articles_by_category()
    {
        $category = Category::factory()->create();
        Article::factory()->create(['category_id' => $category->id, 'status' => true]);
        Article::factory()->create(['category_id' => null, 'status' => true]);

        $response = $this->json('GET', '/api/v1/articles', ['category_id' => $category->id]);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_filters_articles_by_date_range()
    {
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        Article::factory()->create(['published_at' => $startDate->copy()->subDay(), 'status' => true]);
        Article::factory()->create(['published_at' => $startDate->copy()->addDay(), 'status' => true]);
        Article::factory()->create(['published_at' => $endDate->copy()->addDay(), 'status' => true]);

        $response = $this->json('GET', '/api/v1/articles', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_returns_an_article_when_slug_exists()
    {
        $article = Article::factory()->create([
            'slug' => 'test-article',
        ]);

        $response = $this->json('GET', '/api/v1/articles/test-article');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $article->id,
                'slug' => 'test-article',
                'title' => $article->title,
            ]);
    }

    public function test_returns_not_found_when_slug_does_not_exist()
    {
        $response = $this->json('GET', '/api/v1/articles/non-existent-article');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'Article not found',
            ]);
    }
}
