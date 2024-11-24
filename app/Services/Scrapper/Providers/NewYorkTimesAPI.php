<?php

namespace App\Services\Scrapper\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewYorkTimesAPI
{
    private object $response;
    private array|string $body;
    protected string $apiKey;
    protected string $apiEndpoint;

    public function __construct()
    {
        $this->apiKey = config('newsapis.newyorktimes.api_key') ?? '';
        $this->apiEndpoint = config('newsapis.newyorktimes.base_url') ?? '';
        $this->body = [];

        $this->response = Http::withOptions(["retry_on_failure" => 2])->acceptJson();
    }

    public function getNews($page)
    {
        $offset = $this->getOffset($page);
        $this->setApiEndpoint("content/all/all.json?api-key={$this->apiKey}&limit=10&offset={$offset}");
        $result = $this->sendRequest();

        if ($result->ok()) {
            $json_body = $result->json();

            $articles = [];
            foreach ($json_body['results'] as $index => $article) {

                $content = "No content available";
                if (isset($article['abstract'])) {
                    $content = $article['abstract'];
                    $content = html_entity_decode(strip_tags($content));
                    $content = Str::of($content)->trim();
                }

                $category = $this->inferCategoryFromSource($article['source']);
                $published_at = Carbon::parse($article['published_date'])->format('Y-m-d H:i:s');

                $articles[$index] = [
                    'title' => $article['title'],
                    'slug' => Str::slug($article['title']),
                    'source' => $article['source'],
                    'author' => 'unknown',
                    'category' => strtolower($category),
                    'description' => $article['abstract'] ?? 'No description available',
                    'url' => $article['url'],
                    'image_url' => $article['multimedia'][0]['url'] ?? '',
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

    private function getOffset($page)
    {
        return ($page - 1) * 10;
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
