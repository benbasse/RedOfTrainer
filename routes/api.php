<?php

use App\Http\Controllers\API\FactureController;
use App\Http\Controllers\API\FormationController;
use App\Http\Controllers\API\TrainerController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClientController;
use App\Models\Formation;
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

// Facture
Route::get('Facture/list', [FactureController::class, 'index']);
Route::get('Facture/about/{id}', [FactureController::class, 'show']);
Route::delete('Facture/delete/{id}', [FactureController::class, 'destroy']);


//Clients
Route::get('Clients', [ClientController::class, 'index']);


// formations
Route::get('Formations', [FormationController::class, 'index']);

//Trainer
Route::post('trainer/register', [TrainerController::class, 'register']);
Route::delete('Trainer/deleteAccount/{id}', [TrainerController::class, 'destroy']);
Route::get('Trainer/list', [TrainerController::class, 'index']);

Route::middleware(['auth:api', 'access:trainer'])->group(function(){
    //trainer
    Route::post('Trainer/editAccount/{id}' ,[TrainerController::class, 'updateTrainer']);
    Route::post('Trainer/editPassword', [TrainerController::class, 'editPassword']);
    Route::get('Trainer/about/{id}', [TrainerController::class, 'show']);

    //formations
    Route::post('Formation/add', [FormationController::class, 'store']);
    Route::delete('Formation/delete/{id}', [FormationController::class, 'destroy']);
    Route::get('Formation/about/{id}', [FormationController::class, 'show']);
    Route::put('Formation/edit/{id}', [FormationController::class, 'update']);
    Route::get('Formation/listFormationTrainer', [FormationController::class, 'listFormationTrainer']);

    //Clients
    Route::get('Client/listClientTrainer', [ClientController::class, 'listClientTrainer']);
    Route::post('Client/add', [ClientController::class, 'store']);
    Route::put('Client/edit/{id}', [ClientController::class, 'update']);
    Route::get('Client/about/{id}', [ClientController::class, 'show']);
    Route::delete('Client/delete/{id}', [ClientController::class, 'destroy']);

    //Facture
    Route::post('Facture/add', [FactureController::class, 'store']);
    Route::put('Facture/edit/{id}', [FactureController::class, 'update']);
    Route::get('Facture/ListFactureTrainer', [FactureController::class, 'ListFactureTrainer']);
});