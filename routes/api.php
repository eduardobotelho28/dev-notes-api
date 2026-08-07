<?php

use App\Http\Controllers\Api\ConceptController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ImageUploadController;

Route::get('/concepts', [ConceptController::class, 'index']);
Route::get('/concepts/{concept:slug}', [ConceptController::class, 'show']);

Route::middleware('admin.auth')->group(function () {
    Route::post('/concepts', [ConceptController::class, 'store']);
    Route::delete('/concepts/{concept}', [ConceptController::class, 'destroy']);
    Route::put('/concepts/{concept}', [ConceptController::class, 'update']);
    Route::post('/uploads/image', [ImageUploadController::class, 'store']);
});

Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1');