<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventCategoryController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->merge([
            'name' => trim((string) $request->input('name')),
        ]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ], [], ['name' => 'etkinlik türü']);

        $existing = EventCategory::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['name'])])
            ->first();

        $category = $existing ?? EventCategory::query()->create($data);

        return response()->json([
            'message' => $existing ? 'Bu tür zaten vardı, seçildi.' : 'Etkinlik türü eklendi.',
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ],
        ], $existing ? 200 : 201);
    }
}
