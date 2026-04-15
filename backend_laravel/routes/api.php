<?php

use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\PurchaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*
|--------------------------------------------------------------------------
| Achievement & Badge Routes
|--------------------------------------------------------------------------
*/
Route::prefix('users/{user}')->group(function () {
    // Main achievements endpoint (required by assessment)
    Route::get('/achievements', [AchievementController::class, 'show'])
        ->name('api.users.achievements');
    
    // Achievement history timeline (bonus feature)
    Route::get('/achievements/history', [AchievementController::class, 'history'])
        ->name('api.users.achievements.history');
});

/*
|--------------------------------------------------------------------------
| Purchase Routes (for testing/demo)
|--------------------------------------------------------------------------
*/
Route::prefix('users/{user}')->group(function () {
    // Simulate a purchase (for demo purposes)
    Route::post('/purchases', [PurchaseController::class, 'store'])
        ->name('api.users.purchases.store');

    // Reset demo progress for repeatable testing
    Route::post('/reset-progress', [PurchaseController::class, 'resetProgress'])
        ->name('api.users.reset-progress');
    
    // Get purchase history
    Route::get('/purchases', [PurchaseController::class, 'index'])
        ->name('api.users.purchases.index');
});
