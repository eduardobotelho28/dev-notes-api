<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Concept;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConceptController extends Controller
{
    public function index(Request $request)
    {
        $query = Concept::query()->with(['tags', 'links']);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('tldr', 'like', "%{$search}%");
            });
        }

        if ($tag = $request->query('tag')) {
            $query->whereHas('tags', function ($q) use ($tag) {
                $q->where('slug', $tag);
            });
        }

        $orderBy = $request->query('order_by', 'created_at');
        $orderDir = $request->query('order', 'desc');

        $allowedOrderFields = ['created_at', 'title', 'updated_at'];
        if (!in_array($orderBy, $allowedOrderFields)) {
            $orderBy = 'created_at';
        }
        $orderDir = strtolower($orderDir) === 'asc' ? 'asc' : 'desc';

        $query->orderBy($orderBy, $orderDir);

        $perPage = (int) $request->query('per_page', 15);
        $perPage = min(max($perPage, 1), 50); // limite de segurança

        return $query->paginate($perPage);
    }

    public function show(Concept $concept)
    {
        return $concept->load(['tags', 'links']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'tldr'          => 'required|string|max:255',
            'summary'       => 'required|string',
            'field_notes'   => 'nullable|string',
            'image_path'    => 'nullable|string',
            'tags'          => 'array',
            'tags.*'        => 'exists:tags,id',
            'links'         => 'array',
            'links.*.title' => 'required_with:links|string',
            'links.*.url'   => 'required_with:links|url',
            'links.*.type'  => 'nullable|string',
        ]);

        $concept = Concept::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'tldr' => $validated['tldr'],
            'summary' => $validated['summary'],
            'field_notes' => $validated['field_notes'] ?? null,
            'image_path' => $validated['image_path'] ?? null,
        ]);

        if (!empty($validated['tags'])) {
            $concept->tags()->sync($validated['tags']);
        }

        if (!empty($validated['links'])) {
            $concept->links()->createMany($validated['links']);
        }

        return response()->json($concept->load(['tags', 'links']), 201);
    }

    public function destroy(Concept $concept)
    {
        $concept->delete();

        return response()->json(null, 204);
    }
}