<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\TransactionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::prefix('user')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/{user_id}/subscription', [SubscriptionController::class, 'createSubscription']);
        Route::put('/{user_id}/subscription/{subscription_id}', [SubscriptionController::class, 'updateSubscription']);
        Route::delete('/{user_id}/subscription/{subscription_id}', [SubscriptionController::class, 'deleteSubscription']);
        Route::get('/{user_id}', [SubscriptionController::class, 'listSubscriptions']);

        Route::post('/{user_id}/transaction', [TransactionController::class, 'createTransaction']);
    });
});
