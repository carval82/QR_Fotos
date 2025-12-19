<?php

use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\AdminPhotoController;
use App\Http\Controllers\PublicUploadController;
use App\Http\Controllers\ScreenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/q/{token}', [PublicUploadController::class, 'show'])->name('q.show');
Route::post('/q/{token}/upload', [PublicUploadController::class, 'upload'])->name('q.upload');

Route::get('/screen/{token}', [ScreenController::class, 'show'])->name('screen.show');
Route::get('/screen/{token}/photos', [ScreenController::class, 'photos'])->name('screen.photos');

Route::prefix('admin')->middleware('admin.basic')->group(function () {
    Route::get('/events', [AdminEventController::class, 'index'])->name('admin.events.index');
    Route::post('/events', [AdminEventController::class, 'store'])->name('admin.events.store');

    Route::get('/events/{event}/moderation', [AdminPhotoController::class, 'moderation'])->name('admin.events.moderation');
    Route::get('/events/{event}/qr', [AdminEventController::class, 'qr'])->name('admin.events.qr');
    Route::post('/photos/{photo}/approve', [AdminPhotoController::class, 'approve'])->name('admin.photos.approve');
    Route::post('/photos/{photo}/reject', [AdminPhotoController::class, 'reject'])->name('admin.photos.reject');
});
