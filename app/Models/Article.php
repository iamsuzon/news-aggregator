<?php

namespace App\Models;

use App\Enums\PostVisibilityEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'category_id',
        'source_id',
        'author_id',
        'description',
        'content',
        'url',
        'image_url',
        'published_at',
        'status',
        'visibility',
        'type',
        'scraped_source',
        'scraped_index',
    ];

    protected $casts = [
        'title' => 'string',
        'slug' => 'string',
        'type' => 'string',
        'category_id' => 'integer',
        'source_id' => 'integer',
        'author_id' => 'integer',
        'description' => 'string',
        'content' => 'string',
        'url' => 'string',
        'image_url' => 'string',
        'published_at' => 'datetime',
        'scraped_source' => 'string',
        'scraped_index' => 'integer',
        'status' => 'boolean',
        'visibility' => PostVisibilityEnum::class
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    public function author()
    {
        return $this->belongsTo(Author::class);
    }
}
