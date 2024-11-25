<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Models\UserPreference;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ArticleManageController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['category', 'source', 'author'])->where('status', true);

        if ($request->has('keyword')) {
            $query->where('title', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('source_id')) {
            $query->where('source_id', $request->source_id);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $start_date = Carbon::parse($request->start_date)->startOfDay();
            $end_date = Carbon::parse($request->end_date)->endOfDay();

            $query->whereBetween('published_at', [$start_date, $end_date]);
        }

        $articles = $query->paginate(10);

        return response()->json([
            'data' => ArticleResource::collection($articles->items()),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'last_page' => $articles->lastPage(),
            ],
        ]);
    }

    public function show($slug)
    {
        $article = Article::where('slug', $slug)->first();

        if (!$article) {
            return response()->json(['message' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($article, Response::HTTP_OK);
    }

    public function personalizedFeed()
    {
        $preferences = UserPreference::where('user_id', auth()->id())->first();

        if (!$preferences) {
            return response()->json([
                'message' => 'No preferences found. Please set your preferences first.'
            ], Response::HTTP_NOT_FOUND);
        }

        $articles = Article::with(['category', 'source', 'author'])
            ->when($preferences->preferred_sources, function ($query, $sources) {
                return $query->whereIn('source_id', $sources);
            })
            ->when($preferences->preferred_categories, function ($query, $categories) {
                return $query->whereIn('category_id', $categories);
            })
            ->when($preferences->preferred_authors, function ($query, $authors) {
                return $query->whereIn('author_id', $authors);
            })
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'data' => ArticleResource::collection($articles->items()),
            'pagination' => [
                'current_page' => $articles->currentPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'last_page' => $articles->lastPage(),
            ],
        ], Response::HTTP_OK);
    }
}
