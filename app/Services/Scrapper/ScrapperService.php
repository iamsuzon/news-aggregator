<?php

declare(strict_types=1);

namespace App\Services\Scrapper;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\Scrapper\Providers\NewYorkTimesAPI;
use App\Services\Scrapper\Providers\GuardianAPI;
use App\Services\Scrapper\Providers\NewsAPI;
use DateTime;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ScrapperService
{
    public static function providers(): array
    {
        return [
            "newsapi" => NewsAPI::class,
            "guardian" => GuardianAPI::class,
            "newyorktimes" => NewYorkTimesAPI::class
        ];
    }

    public static function scrapArticles($provider_class, $provider_name, $page): void
    {
        try {
            $result = (new $provider_class())->getNews($page);

            if ($result['status']) {
                foreach ($result['articles'] as $item) {
                    if ($item['slug'] === 'removed') {
                        continue;
                    }

                    $validDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $item['published_at']);
                    if ($validDateTime->format('Y') <= 1970) {
                        continue;
                    }

                    $category = Category::updateOrCreate(
                        [
                            'slug' => Str::slug($item['category'])
                        ],
                        [
                            'name' => $item['category'],
                            'slug' => Str::slug($item['category']),
                            'status' => true
                        ]
                    );

                    $author = Author::updateOrCreate(
                        [
                            'slug' => Str::slug($item['author'])
                        ],
                        [
                            'name' => $item['author'],
                            'slug' => Str::slug($item['author']),
                            'status' => true
                        ]
                    );

                    $source = Source::updateOrCreate(
                        [
                            'slug' => Str::slug($item['source'])
                        ],
                        [
                            'name' => $item['source'],
                            'slug' => Str::slug($item['source']),
                            'status' => true
                        ]
                    );

                    Article::firstOrCreate([
                        'title' => $item['title'],
                        'slug' => substr($item['slug'], 0, 255),
                        'category_id' => $category->id,
                        'source_id' => $source->id,
                        'author_id' => $author->id,
                        'description' => $item['description'],
                        'content' => $item['content'],
                        'url' => $item['url'],
                        'image_url' => $item['image_url'],
                        'published_at' => $item['published_at'],
                        'status' => true,
                        'visibility' => 1,
                        'type' => 'article',
                        'scraped_source' => $provider_name,
                        'scraped_index' => $page
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Scrapper failed: ' . $e->getMessage());
        }
    }

    public static function lastScrapedPage()
    {
        return Article::max('scraped_index') + 1;
    }
}
