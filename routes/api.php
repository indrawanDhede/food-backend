<?php

use App\Http\Controllers\API\FoodController;
use App\Http\Controllers\API\MidtransController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [UserController::class, 'login']);
Route::post('register', [UserController::class, 'register']);
Route::get('food', [FoodController::class, 'all']);
Route::post('transaction/{id}', [TransactionController::class, 'update']);
Route::post('midtrans/callback', [MidtransController::class, 'callback']);

Route::middleware('auth:sanctum')->group(function(){
    // route mengambil data user
    Route::get('user', [UserController::class, 'fetch']);
    // route untuk update user
    Route::post('user', [UserController::class, 'updateProfile']);
    // route untuk update foto user
    Route::post('user/photo', [UserController::class, 'updatePhoto']);
    // route untuk logout
    Route::post('logout', [UserController::class, 'logout']);
    // route transaction
    Route::get('transaction', [TransactionController::class, 'all']);
    // Route checkout
    Route::post('checkout', [TransactionController::class, 'checkout']);

});
