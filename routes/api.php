<?php

use App\Http\Controllers\OfferController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

// Authentification
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/company/register', [AuthController::class, 'registerCompany']);
    Route::post('/freelance/register', [AuthController::class, 'registerFreelance']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Users
Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{user}', [UserController::class, 'show']);
Route::post('/toggleUser/{user}', [UserController::class, 'toggleValidationUser']);
Route::post('/editUser/{user}', [UserController::class, 'update']);
Route::post('/deleteUser/{user}', [UserController::class, 'destroy']);

// Offers
Route::get('/offers', [OfferController::class, 'index']);
Route::get('/offers/{offer}', [OfferController::class, 'show']);
Route::post('/offers/', [OfferController::class, 'create']);
Route::post('/editOffer/{offer}', [OfferController::class, 'update']);
Route::post('/deleteOffer/{offer}', [OfferController::class, 'destroy']);

// Password Reset
Route::group([
    'prefix' => 'password'
],function (){
    Route::post('create', [PasswordController::class, 'create']);
    Route::post('reset', [PasswordController::class, 'reset']);
});
