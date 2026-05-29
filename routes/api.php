<?php

use App\Helpers\ApiResponse;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\UserVisitController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', fn () => ApiResponse::ok([
    'service' => config('app.name'),
    'environment' => config('app.env'),
    'cicd' => 'not ok'
], 'pong'));

Route::prefix('news')->group(function () {
    Route::get('/', [NewsController::class, 'index']);
    Route::post('/', [NewsController::class, 'store']);
    Route::get('/{id}', [NewsController::class, 'show']);
    Route::put('/{id}', [NewsController::class, 'update']);
    Route::delete('/{id}', [NewsController::class, 'destroy']);
});

// Track user visits
Route::post('/track-visit', [UserVisitController::class, 'track']);
Route::get('/track-visit/{id}', [UserVisitController::class, 'show']);

Route::post('/contact', [ContactController::class, 'store']);
