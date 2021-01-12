<?php

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/company/register', [AuthController::class, 'registerCompany']);
    Route::post('/freelance/register', [AuthController::class, 'registerFreelance']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/me', [AuthController::class, 'me']);
});


Route::post('/toggleUser', [UserController::class, 'toggleValidationUser']);
Route::get('/users', [UserController::class, 'getAllUsers']);

Route::group([
    'prefix' => 'password'
],function (){
    Route::post('create', [PasswordController::class, 'create']);
    Route::post('reset', [PasswordController::class, 'reset']);
});
