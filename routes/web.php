<?php

use Illuminate\Support\Facades\Route;

// Importação organizada por categoria
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\SetupPasswordController;
use App\Http\Controllers\Admin\CredentialController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'welcome');

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

Route::prefix('set-password')->name('password.')->group(function () {
    Route::get('/{user}', [SetupPasswordController::class, 'create'])
        ->middleware('signed')
        ->name('setup');

    Route::post('/{user}', [SetupPasswordController::class, 'store'])
        ->name('set_password_update');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    Route::view('/dashboard', 'dashboard')->name('dashboard');

    // Gestão de Perfil
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Módulo de Usuários
    Route::post('users/{user}/resend-invite', [UserController::class, 'resendInvite'])->name('users.resend-invite');
    Route::resource('users', UserController::class);

    // Área Administrativa
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('credentials', CredentialController::class)->only(['index', 'store', 'destroy']);
    });

});