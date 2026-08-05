<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use RuntimeException;

class ImageUploadController extends Controller
{
    public function __construct(private ImageUploadService $uploadService) {}

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|file|image|max:2048', 
        ]);

        try {
            $path = $this->uploadService->store($request->file('image'));
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'path' => $path,
            'url' => asset('storage/' . $path),
        ], 201);
    }
}