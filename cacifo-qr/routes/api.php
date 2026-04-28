<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LockerController;

Route::get('/locker/{id}/status', [LockerController::class, 'getStatus']);
Route::post('/locker/{id}/confirm-open', [LockerController::class, 'confirmOpen']);
