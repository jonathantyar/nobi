<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\InvestmentProductController;

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

Route::prefix('v1')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | User Routes
    |--------------------------------------------------------------------------
    |
    | For authenticated users, creating a new user
    |
    */
    Route::post('/user/add', [AuthController::class, 'userAdd'])->name('user.add');
    Route::post('/user/authenticate', [AuthController::class, 'userAuthenticate'])->name('user.authenticate');

    Route::name('investment.')->prefix('/{investment_product:code}')->group(function () {
        Route::post('/updateTotalBalance', [InvestmentProductController::class, 'updateTotalBalance'])->name('update.balance');

        Route::get('/listNAB', [InvestmentProductController::class, 'listNAB'])->name('list.nab');

        Route::post('/topup', [InvestmentProductController::class, 'topup'])->name('topup');
        Route::post('/withdraw', [InvestmentProductController::class, 'withdraw'])->name('withdraw');
    });
});
