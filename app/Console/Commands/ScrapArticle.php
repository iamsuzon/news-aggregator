<?php

namespace App\Console\Commands;

use App\Jobs\ScrapArticleJob;
use App\Services\Scrapper\ScrapperService;
use Illuminate\Console\Command;

class ScrapArticle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrap:article';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $providers = ScrapperService::providers();

        $page = ScrapperService::lastScrapedPage();
        foreach ($providers as $provider_name => $provider_class) {
            ScrapArticleJob::dispatch($provider_class, $provider_name, $page);
        }
    }
}
