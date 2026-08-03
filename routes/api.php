<?php

use App\Http\Controllers\Api\ConceptController;

Route::get('/concepts', [ConceptController::class, 'index']);
Route::get('/concepts/{concept:slug}', [ConceptController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/concepts', [ConceptController::class, 'store']);
    Route::delete('/concepts/{concept}', [ConceptController::class, 'destroy']);
});