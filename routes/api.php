<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TenantSyncController;
use App\Http\Controllers\Api\Omie\OmieBillingController;
use App\Http\Controllers\ClientController;

Route::prefix('n8n')->group(function () {
    Route::get('/tenants', [TenantSyncController::class, 'index']);
    Route::post('/titles/sync', [TenantSyncController::class, 'storeTitles']);
});

Route::prefix('omie')->group(function () {
    Route::post('/billings/receive', [OmieBillingController::class, 'receiveFromN8n']);
    Route::post('/sync/clients', [ClientController::class, 'storeFromN8n']);
});
