<?php

use App\Enums\PostVisibilityEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->index()->unique();
            $table->unsignedBigInteger('category_id')->index()->nullable();
            $table->unsignedBigInteger('source_id')->index()->nullable();
            $table->unsignedBigInteger('author_id')->index()->nullable();
            $table->longText('description');
            $table->longText('content');
            $table->longText('url')->nullable();
            $table->longText('image_url')->nullable();
            $table->timestamp('published_at');
            $table->boolean('status')->default(false);
            $table->enum('visibility', array_column(PostVisibilityEnum::cases(), 'value'))->default(PostVisibilityEnum::Public);
            $table->string('type')->default('article');
            $table->string('scraped_source')->comment('The source of the scraped data');
            $table->unsignedBigInteger('scraped_index')->comment('The index of the scraped page');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
