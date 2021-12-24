<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\api\RekeningController;
use App\Http\Controllers\api\TransaksiController;
use App\Http\Controllers\api\MutasiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);
    // Route::get('user', [UserController::class, 'getAuthenticatedUser'])->middleware('jwt.verify');
});

Route::group(['prefix' => 'rekening', 'middleware' => ['jwt.verify', 'admin']], function () {
    Route::get('/', [RekeningController::class, 'index']);
    Route::get('/{id}', [RekeningController::class, 'show']);
    Route::post('/store', [RekeningController::class, 'store']);
    Route::patch('/update/{id}', [RekeningController::class, 'update']);
});

Route::group(['prefix' => 'transaksi', 'middleware' => 'jwt.verify'], function () {
    Route::get('/', [TransaksiController::class, 'index']);
    Route::post('/topup', [TransaksiController::class, 'topup']);
    Route::post('/wd', [TransaksiController::class, 'wd']);
    Route::post('/tf', [TransaksiController::class, 'tf']);
    Route::get('/mutasi', [TransaksiController::class, 'mutasi']);
});

Route::group(['prefix' => 'mutasi', 'middleware' => ['jwt.verify', 'admin']], function () {
    Route::get('/', [MutasiController::class, 'mutasi']);
});
