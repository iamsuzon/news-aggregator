<?php

namespace Database\Seeders;

use App\Models\User;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Artisan::call('scrap:article');
        Artisan::call('queue:work', [
            '--timeout' => 60,
            '--tries' => 1,
            '--delay' => 1,
            '--stop-when-empty' => true
        ]);
    }
}
