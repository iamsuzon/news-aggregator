<?php

namespace App\Services\Scrapper\Providers;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewsAPI
{
    private object $response;
    private array|string $body;
    protected string $apiKey;
    protected string $apiEndpoint;

    public function __construct()
    {
        $this->apiKey = config('newsapis.newsapi.api_key') ?? '';
        $this->apiEndpoint = config('newsapis.newsapi.base_url') ?? '';
        $this->body = [];

        $this->response = Http::withOptions(["retry_on_failure" => 2])->withHeaders([
            'Authorization' => $this->apiKey
        ])->acceptJson();
    }

    public function getNews($page)
    {
        $this->setApiEndpoint("top-headlines?country=us&pageSize=10&page={$page}");
        $result = $this->sendRequest();

        if ($result->ok()) {
            $json_body = $result->json();

            $articles = [];
            foreach ($json_body['articles'] as $index => $article) {

                $content = "No content available";
                if (isset($article['content'])) {
                    $content = $article['content'];
                    $content = html_entity_decode(strip_tags($content));
                    $content = Str::of($content)->trim();
                }

                $category = $this->inferCategoryFromSource($article['source']['name']);
                $published_at = Carbon::parse($article['publishedAt'])->format('Y-m-d H:i:s');

                $articles[$index] = [
                    'title' => $article['title'],
                    'slug' => Str::slug($article['title']),
                    'source' => $article['source']['name'],
                    'author' => $article['author'] ?? 'unknown',
                    'category' => strtolower($category),
                    'description' => $article['description'] ?? 'No description available',
                    'url' => $article['url'],
                    'image_url' => $article['urlToImage'],
                    'published_at' => $published_at,
                    'content' => $content,
                    'status' => true,
                    'visibility' => 1,
                    'type' => 'article',
                    'scraped_index' => $page,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            return [
                'status' => true,
                'articles' => $articles
            ];
        }

        return [
            'status' => false
        ];
    }

    private function inferCategoryFromSource($sourceName): string
    {
        switch ($sourceName) {
            case 'BBC News':
            case 'CNN':
            case 'The New York Times':
            case 'The Washington Post':
                return 'general';
            case 'ESPN':
            case 'Fox Sports':
            case 'The Sport Bible':
                return 'sports';
            case 'TechCrunch':
            case 'The Next Web':
            case 'Wired':
                return 'technology';
            case 'National Geographic':
            case 'New Scientist':
                return 'science';
            case 'Business Insider':
            case 'Financial Times':
            case 'The Wall Street Journal':
                return 'business';
            case 'Entertainment Weekly':
            case 'MTV News':
            case 'The Lad Bible':
                return 'entertainment';
            case 'Medical News Today':
            case 'The American Journal of Clinical Nutrition':
                return 'health';
            default:
                return 'other';
        }
    }

    private function setApiEndpoint(string $endpoint)
    {
        $hasSlash = Str::of($endpoint)->startsWith('/');

        if ($hasSlash) {
            $this->apiEndpoint = $this->apiEndpoint . $endpoint;
            return;
        }

        $this->apiEndpoint = $this->apiEndpoint . '/' . $endpoint;
    }

    private function sendRequest()
    {
        if (!empty($this->body)) {
            return $this->response->get($this->apiEndpoint, $this->body);
        }

        return $this->response->get($this->apiEndpoint);
    }
}
