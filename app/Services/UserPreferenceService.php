<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\UserPreference;

class UserPreferenceService
{
    public static function getPreferences(UserPreference $preferences): array
    {
        $sources_id = $preferences->preferred_sources;
        $categories_id = $preferences->preferred_categories;
        $authors_id = $preferences->preferred_authors;

        $sources = $sources_id ? Source::select('id', 'name', 'slug')->whereIn('id', $sources_id)->get()->toArray() : [];
        $categories = $categories_id ? Category::select('id', 'name', 'slug')->whereIn('id', $categories_id)->get()->toArray() : [];
        $authors = $authors_id ? Author::select('id', 'name', 'slug')->whereIn('id', $authors_id)->get()->toArray() : [];

        return [
            'preferred_sources' => $sources,
            'preferred_categories' => $categories,
            'preferred_authors' => $authors,
        ];
    }
}
