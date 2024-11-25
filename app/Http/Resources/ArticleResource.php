<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'category' => $this->category,
            'source' => $this->source,
            'image_url' => $this->image_url,
            'published_at' => $this->published_at->toDateTimeString(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
