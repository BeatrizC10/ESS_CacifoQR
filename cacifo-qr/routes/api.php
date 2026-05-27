<?php

use Illuminate\Support\Facades\Route;
use App\Models\Locker;

// Rota pública — não precisa de autenticação
Route::get('/locker/{id}/status', function (int $id) {
    $locker = Locker::findOrFail($id);
    return response()->json([
        'door_open' => $locker->door_open,
        'status'    => $locker->status,
    ]);
})->withoutMiddleware(['auth:sanctum', 'auth']);

Route::post('/locker/{id}/confirm-open', [App\Http\Controllers\LockerController::class, 'confirmOpen']);
