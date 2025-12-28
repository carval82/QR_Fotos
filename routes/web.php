<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminPhotoController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\PublicUploadController;
use App\Http\Controllers\ScreenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::get('/q/{token}', [PublicUploadController::class, 'show'])->name('q.show');
Route::post('/q/{token}/upload', [PublicUploadController::class, 'upload'])->name('q.upload');

// Mensajes públicos
Route::get('/m/{token}', [MessageController::class, 'show'])->name('messages.show');
Route::post('/m/{token}', [MessageController::class, 'store'])->name('messages.store');

Route::get('/screen/{token}', [ScreenController::class, 'show'])->name('screen.show');
Route::get('/screen/{token}/photos', [ScreenController::class, 'photos'])->name('screen.photos');

// Admin login routes
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware('admin.basic')->group(function () {
    Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');
    Route::delete('/events/{event}', [AdminEventController::class, 'destroy'])->name('admin.events.destroy');

    Route::get('/events/{event}/moderation', [AdminPhotoController::class, 'moderation'])->name('admin.events.moderation');
    Route::get('/events/{event}/qr', [AdminEventController::class, 'qr'])->name('admin.events.qr');
    Route::post('/photos/{photo}/approve', [AdminPhotoController::class, 'approve'])->name('admin.photos.approve');
    Route::post('/photos/{photo}/reject', [AdminPhotoController::class, 'reject'])->name('admin.photos.reject');

    // Mensajes admin
    Route::get('/events/{event}/messages', [AdminMessageController::class, 'index'])->name('admin.events.messages');
    Route::post('/messages/{message}/read', [AdminMessageController::class, 'markAsRead'])->name('admin.messages.read');
    Route::delete('/messages/{message}', [AdminMessageController::class, 'destroy'])->name('admin.messages.destroy');
});
