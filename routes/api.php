<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ScheduledTransactionController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TransactionController;
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


Route::prefix('v1')->group(function () {

    //----------------- ROUTES CLIENT:
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/create', [ClientController::class, 'register']);
        // Exemple dans routes/web.php ou routes/api.php

        Route::post('/test', function () {
            return response()->json(['message' => 'POST route works']);
        });

        Route::post('/login', [ClientController::class, 'login']);
        Route::middleware('auth:api')->post('/logout', [ClientController::class, 'logout'])->name('user.logout');

        Route::middleware('auth:api')->get('/balance', [ClientController::class, 'getBalance']);
    });



    //----------------- ROUTES TRANSACTIONS:
    Route::prefix('transactions')->group(function () {
        Route::middleware('auth:api')->post('/send', [TransactionController::class, 'sendMoney']);
        Route::middleware('auth:api')->get('/historique', [TransactionController::class, 'index']);
        Route::middleware('auth:api')->post('/send-multiple', [TransactionController::class, 'sendMultiple']);
        Route::middleware('auth:api')->post('/{transactionId}/cancel', [TransactionController::class, 'cancel']);
        Route::middleware('auth:api')->get('/sheduled', [ScheduledTransactionController::class, 'index']);
        Route::middleware('auth:api')->post('/planification', [ScheduledTransactionController::class, 'store']);
        Route::middleware('auth:api')->post('/cancelShedule/{transactionId}', [ScheduledTransactionController::class, 'cancel']);
    });

    //----------------- ROUTES SERVICES:
    Route::prefix('services')->group(function () {
        Route::middleware('auth:api')->get('/{serviceId}', [ServiceController::class, 'show']);
        Route::middleware('auth:api')->get('/', [ServiceController::class, 'index']);
    
    });
});


