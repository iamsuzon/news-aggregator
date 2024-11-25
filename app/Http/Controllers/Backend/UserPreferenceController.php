<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\UserPreference;
use App\Services\UserPreferenceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class UserPreferenceController extends Controller
{
    public function show()
    {
        $user_id = auth()->id();
        $preferences = UserPreference::where('user_id', $user_id)->first();

        if ($preferences) {
            $preferences = UserPreferenceService::getPreferences($preferences);
        }

        return response()->json($preferences ?? [], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'preferred_sources' => 'nullable|array',
            'preferred_sources.*' => 'integer',
            'preferred_categories' => 'nullable|array',
            'preferred_categories.*' => 'integer',
            'preferred_authors' => 'nullable|array',
            'preferred_authors.*' => 'integer',
        ]);

        if ($validated->fails()) {
            return response()->json(['errors' => $validated->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $data = $validated->validated();

        $preferences = UserPreference::updateOrCreate(
            ['user_id' => auth()->id()],
            [
                'preferred_sources' => $data['preferred_sources'] ?? [],
                'preferred_categories' => $data['preferred_categories'] ?? [],
                'preferred_authors' => $data['preferred_authors'] ?? [],
            ]
        );

        return response()->json([
            'message' => 'Preferences updated successfully',
            'preferences' => $preferences
        ], Response::HTTP_CREATED);
    }
}
