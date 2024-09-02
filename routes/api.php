<?php

use App\Http\Controllers\API\TrainerController;
use App\Http\Controllers\API\AuthController;
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

Route::group([

    'middleware' => 'api',

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class,'refresh']);
    Route::post('me', [AuthController::class,'me']);

});


//Trainer
Route::post('trainer/register', [TrainerController::class, 'register']);
Route::delete('Trainer/deleteAccount/{id}', [TrainerController::class, 'destroy']);
Route::get('Trainer/list', [TrainerController::class, 'index']);

Route::middleware(['auth:api', 'access:trainer'])->group(function(){
    Route::post('Trainer/editAccount/{id}' ,[TrainerController::class, 'updateTrainer']);
    Route::post('Trainer/editPassword', [TrainerController::class, 'editPassword']);
    Route::get('Trainer/about/{id}', [TrainerController::class, 'show']);
});