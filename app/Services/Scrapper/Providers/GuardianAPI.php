<?php

namespace App\Services\Scrapper\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GuardianAPI
{
    private object $response;
    private array|string $body;
    protected string $apiKey;
    protected string $apiEndpoint;

    public function __construct()
    {
        $this->apiKey = config('newsapis.guardian.api_key') ?? '';
        $this->apiEndpoint = config('newsapis.guardian.base_url') ?? '';
        $this->body = [];

        $this->response = Http::withOptions(["retry_on_failure" => 2])->withHeaders([
            'api-key' => $this->apiKey
        ])->acceptJson();
    }

    public function getNews($page)
    {
        $this->setApiEndpoint("search");
        $result = $this->sendRequest($page);

        if ($result->ok()) {
            $json_body = $result->json();
            $response = $json_body['response'] ?? [];

            $articles = [];
            foreach ($response['results'] as $index => $article) {
                $source = "Unknown";
                foreach ($article['tags'] as $tag) {
                    if ($tag['type'] == 'contributor') {
                        $source = $tag['webTitle'];
                        break;
                    }
                }

                $description = "No content available";
                if (isset($article['fields']['trailText']))
                {
                    $description = $article['fields']['trailText'];
                    $description = html_entity_decode(strip_tags($description));
                    $description = Str::of($description)->trim();
                }

                $published_at = Carbon::parse($article['webPublicationDate'])->format('Y-m-d H:i:s');

                $articles[$index] = [
                    'title' => $article['webTitle'],
                    'slug' => Str::slug($article['webTitle']),
                    'source' => $source,
                    'category' => $article['pillarName'] ? strtolower($article['pillarName']) : "uncategorized",
                    'author' => $article['fields']['byline'] ?? "unknown",
                    'description' => $description,
                    'url' => $article['webUrl'],
                    'image_url' => $article['fields']['thumbnail'] ?? null,
                    'published_at' => $published_at,
                    'content' => $description,
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

    private function setApiEndpoint(string $endpoint)
    {
        $hasSlash = Str::of($endpoint)->startsWith('/');

        if ($hasSlash) {
            $this->apiEndpoint = $this->apiEndpoint . $endpoint;
            return;
        }

        $this->apiEndpoint = $this->apiEndpoint . '/' . $endpoint;
    }

    private function sendRequest($page)
    {
        return $this->response->get($this->apiEndpoint, [
            'api-key' => 'test',
            'q' => 'debate',
            'show-fields' => 'byline,category,trailText,url,thumbnail,publishedAt,content',
            'show-tags' => 'contributor',
            'pageSize' => 10,
            'page' => $page
        ]);
    }
}
