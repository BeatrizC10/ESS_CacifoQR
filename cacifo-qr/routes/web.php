<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LockerAdminController;
use App\Http\Controllers\LockerController;

Route::get('/', function () {
    return redirect()->route('locker.show', 1);
});

Route::get('/dashboard', function () {
    return redirect()->route('locker.show', 1);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/locker/{id}', [LockerController::class, 'show'])->name('locker.show');

Route::post('/locker/{id}/reserve', [LockerController::class, 'reserve'])
    ->middleware('auth')
    ->name('locker.reserve');

Route::get('/qr-access/{token}', [LockerController::class, 'qrAccess'])
    ->name('locker.qr.access');

Route::get('/api/locker/{id}/status', [LockerController::class, 'getStatus']);
Route::post('/api/locker/{id}/confirm-open', [LockerController::class, 'confirmOpen']);

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/lockers', [LockerAdminController::class, 'index'])->name('admin.lockers.index');
    Route::post('/admin/lockers/{id}/open', [LockerAdminController::class, 'open'])->name('admin.lockers.open');
    Route::post('/admin/lockers/{id}/close', [LockerAdminController::class, 'close'])->name('admin.lockers.close');
    Route::post('/admin/lockers/{id}/reset', [LockerAdminController::class, 'reset'])->name('admin.lockers.reset');

    Route::post('/locker/{id}/open', [LockerController::class, 'openFromUser'])->name('locker.open');
    Route::post('/locker/{id}/close', [LockerController::class, 'closeFromUser'])->name('locker.close');
});

require __DIR__ . '/auth.php';
