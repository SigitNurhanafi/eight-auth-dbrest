<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* |-------------------------------------------------------------------------- | API Routes |-------------------------------------------------------------------------- | | Here is where you can register API routes for your application. These | routes are loaded by the RouteServiceProvider and all of them will | be assigned to the "api" middleware group. Make something great! | */

use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('api:login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get(
        '/user',
        function (Request $request) {
            return $request->user();
        }
    );

    // CRUD Users — PUT dan PATCH dipisah
    Route::apiResource('users', \App\Http\Controllers\Api\UserController::class)
        ->except(['update']);
    Route::put('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'fullUpdate'])
        ->name('users.fullUpdate');
    Route::patch('/users/{user}', [\App\Http\Controllers\Api\UserController::class, 'partialUpdate'])
        ->name('users.partialUpdate');

    Route::get('/data', [\App\Http\Controllers\Api\DataController::class, 'fetchData']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
