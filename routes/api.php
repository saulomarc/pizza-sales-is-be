<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PizzaController;
use App\Http\Controllers\PizzaTypeController;
use App\Http\Controllers\StatController;

Route::post('login', [AuthController::class, 'login']);
Route::post('save-password', [AuthController::class, 'savePassword']);
Route::post('logout', [AuthController::class, 'logout']);
Route::post('read-upload-files', [PizzaController::class, 'readUploadFiles']);

Route::group(['middleware' => ['jwt'], 'prefix' => 'auth'], function () {    
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => ['jwt']], function () {
    Route::apiResource('pizza-types', PizzaTypeController::class);
    Route::apiResource('pizzas', PizzaController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('ingredients', IngredientController::class);
    Route::get('fetch-dashboard-card-stats', [StatController::class, 'fetchDashboardCardStats']);
});