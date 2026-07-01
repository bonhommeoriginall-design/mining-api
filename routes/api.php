<?php

use App\Http\Controllers\Api\AuthController as ApiAuthController;
use App\Http\Controllers\Api\DocumentController as ApiDocumentController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ApiAuthController::class, 'login']);
Route::post('/register', [ApiAuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ApiAuthController::class, 'me']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    Route::get('/documents/sync', [ApiDocumentController::class, 'sync']);
    Route::get('/documents', [ApiDocumentController::class, 'index']);
    Route::get('/documents/{document}/download', [ApiDocumentController::class, 'download'])
        ->name('api.documents.download');
});
