<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\http\Controllers\api\v2\AuthController;

Route::post('/login',[AuthController::class,'login']);