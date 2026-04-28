<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LockerController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/locker/{id}', [LockerController::class, 'show'])->name('locker.show');


Route::get('/api/locker/{id}/status', [LockerController::class, 'getStatus']);
Route::post('/api/locker/{id}/confirm-open', [LockerController::class, 'confirmOpen']);

Route::post('/locker/{id}/reserve', [LockerController::class, 'reserve'])
    ->middleware('auth')
    ->name('locker.reserve');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


