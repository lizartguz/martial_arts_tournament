<?php

use App\Http\Controllers\Api\V1\PublicEventController;
use App\Http\Controllers\Api\V1\PurchaseRequestController;
use App\Http\Controllers\Api\V1\SubscriberProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::get('/events', [PublicEventController::class, 'index'])->name('events.index');
    Route::get('/events/{event:slug}', [PublicEventController::class, 'show'])->name('events.show');
    Route::post('/purchase-requests', [PurchaseRequestController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('purchase-requests.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [SubscriberProfileController::class, 'show'])->name('me.show');
    });
});
