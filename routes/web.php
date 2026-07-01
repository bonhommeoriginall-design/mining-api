<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ChatController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DocumentController;
use App\Http\Controllers\Web\SettingsController;
use App\Http\Controllers\Web\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route(auth()->user()->isUtilisateur() ? 'chat.index' : 'dashboard');
    }

    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');

    Route::redirect('/profile/password', '/settings')->name('profile.password.edit');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/ask', [ChatController::class, 'ask'])->name('chat.ask');
    Route::post('/chat/clear', [ChatController::class, 'clear'])->name('chat.clear');
    Route::post('/chat/restore', [ChatController::class, 'restore'])->name('chat.restore');

    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');

    Route::middleware('role:editor,admin')->group(function () {
        Route::get('/documents/create', [DocumentController::class, 'create'])->name('documents.create');
        Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
        Route::post('/documents/{document}/replace', [DocumentController::class, 'replace'])
            ->name('documents.replace');
        Route::post('/documents/{document}/publish', [DocumentController::class, 'publish'])
            ->name('documents.publish');
    });

    Route::middleware('role:admin')->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
    });
});
