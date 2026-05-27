<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LockerAdminController;
use App\Http\Controllers\LockerController;
use App\Http\Controllers\WalletController;

Route::get('/', [LockerController::class, 'index'])->name('lockers.index');
Route::get('/lockers', [LockerController::class, 'index']);

Route::get('/dashboard', function () {
    return redirect()->route('lockers.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/locker/{id}', [LockerController::class, 'show'])->name('locker.show');

Route::post('/locker/{id}/reserve', [LockerController::class, 'reserve'])
    ->middleware('auth')
    ->name('locker.reserve');

Route::get('/qr-access/{token}', [LockerController::class, 'qrAccess'])
    ->name('locker.qr.access');

Route::get('/api/locker/{id}/qr-data', [LockerController::class, 'getQrData'])
    ->name('locker.qr.data');

Route::post('/locker/{id}/generate-qr', [LockerController::class, 'generateQr'])
    ->middleware('auth')
    ->name('locker.generateQr');

Route::post('/locker/{id}/close', [LockerController::class, 'closeLocker'])
    ->middleware('auth')
    ->name('locker.close');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/admin/lockers', [LockerAdminController::class, 'index'])->name('admin.lockers.index');
    Route::post('/admin/lockers/{id}/open', [LockerAdminController::class, 'open'])->name('admin.lockers.open');
    Route::post('/admin/lockers/{id}/close', [LockerAdminController::class, 'close'])->name('admin.lockers.close');
    Route::post('/admin/lockers/{id}/reset', [LockerAdminController::class, 'reset'])->name('admin.lockers.reset');

    Route::post('/locker/reservation/{reservation}/end', [LockerController::class, 'endReservation'])->name('locker.endReservation');

    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/topup', [WalletController::class, 'topUp'])->name('wallet.topup');
    Route::post('/wallet/card/add', [WalletController::class, 'addCard'])->name('wallet.card.add');
    Route::post('/wallet/card/{id}/default', [WalletController::class, 'setDefaultCard'])->name('wallet.card.default');
});

require __DIR__ . '/auth.php';
