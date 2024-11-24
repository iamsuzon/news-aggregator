<?php

namespace App\Jobs;

use App\Services\Scrapper\ScrapperService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ScrapArticleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $provider_class, public string $provider_name, public int $page)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        ScrapperService::scrapArticles(
            provider_class: $this->provider_class,
            provider_name: $this->provider_name,
            page: $this->page
        );
    }
}
