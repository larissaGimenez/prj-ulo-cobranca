<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\SetupPasswordController;
use App\Http\Controllers\Admin\CredentialController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\LogisticsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\KanbanController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\NegotiationController;
use App\Http\Controllers\DashboardController;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

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

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
        Route::get('tenants/{tenant}/test', [TenantController::class, 'testConnection'])->name('tenants.test-connection');
        Route::resource('tenants', TenantController::class);
    });

    //Roles
    Route::prefix('admin')->group(function () {
        Route::resource('roles', RoleController::class);
    });

    /**
     * MÓDULO: BILLINGS (Cobrança)
     */
    Route::get('/billings', [BillingController::class, 'index'])->name('billings.index');
    Route::post('/billings/stages', [BillingController::class, 'storeStage'])->name('billings.store_stage');
    Route::put('/billings/stages/{id}', [BillingController::class, 'updateStage'])->name('billings.update_stage');
    Route::delete('/billings/stages/{id}', [BillingController::class, 'destroyStage'])->name('billings.destroy_stage');
    Route::post('/billings/operations/{id}/checklist', [BillingController::class, 'addItemToChecklist'])->name('billings.add_checklist_item');
    Route::delete('/billings/operations/{id}/checklist', [BillingController::class, 'removeItemFromChecklist'])->name('billings.remove_checklist_item');
    Route::patch('/billings/operations/{id}/checklist', [BillingController::class, 'updateChecklist'])->name('billings.update_checklist');
    Route::post('/billings/sync', [BillingController::class, 'sync'])->name('billings.sync');
    Route::get('/billings/{id}', [BillingController::class, 'show'])->name('billings.show');

    /**
     * MÓDULO: FINANCES (Financeiro)
     */
    Route::get('finances', [FinanceController::class, 'index'])->name('finances.index');
    Route::post('finances', [FinanceController::class, 'store'])->name('finances.store');
    Route::get('finances/{id}', [FinanceController::class, 'show'])->name('finances.show');
    Route::put('finances/{id}', [FinanceController::class, 'update'])->name('finances.update');
    Route::delete('finances/{id}', [FinanceController::class, 'destroy'])->name('finances.destroy');

    /**
     * MÓDULO: LOGISTICS (Logística)
     */
    Route::get('logistics', [LogisticsController::class, 'index'])->name('logistics.index');
    Route::post('logistics', [LogisticsController::class, 'store'])->name('logistics.store');
    Route::get('logistics/{id}', [LogisticsController::class, 'show'])->name('logistics.show');
    Route::put('logistics/{id}', [LogisticsController::class, 'update'])->name('logistics.update');
    Route::delete('logistics/{id}', [LogisticsController::class, 'destroy'])->name('logistics.destroy');

    /**
     * MÓDULO: PRODUCTS (Produtos)
     */
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::put('products/{id}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

    /**
     * MÓDULO: Supports (Suporte)
     */
    Route::get('supports', [SupportController::class, 'index'])->name('supports.index');
    Route::post('supports', [SupportController::class, 'store'])->name('supports.store');
    Route::get('supports/{id}', [SupportController::class, 'show'])->name('supports.show');
    Route::put('supports/{id}', [SupportController::class, 'update'])->name('supports.update');
    Route::delete('supports/{id}', [SupportController::class, 'destroy'])->name('supports.destroy');

    /**
     * MÓDULO: SALES (Comercial)
     */
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('sales/{id}', [SaleController::class, 'show'])->name('sales.show');
    Route::put('sales/{id}', [SaleController::class, 'update'])->name('sales.update');
    Route::delete('sales/{id}', [SaleController::class, 'destroy'])->name('sales.destroy');

    /**
     * MÓDULO: CLIENTS (Clientes)
     */

    Route::resource('clients', ClientController::class)->only(['index', 'show']);

    // Rota do Kanban
    Route::get('/kanban', [KanbanController::class, 'index'])->name('kanban.index');

    //Modulo Negociações
    Route::resource('negotiations', NegotiationController::class);

});