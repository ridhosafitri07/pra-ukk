<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\ItemController;



Route::get('/items', [ItemController::class, 'index']);
Route::post('/items', [ItemController::class, 'store']);
Route::get('/items/{id}', [ItemController::class, 'show']);
Route::put('/items/{id}', [ItemController::class, 'update']);
Route::delete('/items/{id}', [ItemController::class, 'destroy']);


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',        [AuthController::class, 'me']);
    Route::post('/logout',     [AuthController::class, 'logout']);

    Route::put('/user',               [ProfileController::class, 'update']);
    Route::put('/user/password',      [ProfileController::class, 'changePassword']);



    
    Route::apiResource('items', ItemController::class)->only([
        'index','store','show','update','destroy'
    ]);



    
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API Laravel sudah jalan!',
        ]);
    });


});
