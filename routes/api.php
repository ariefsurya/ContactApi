<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('/contacts', App\Http\Controllers\Api\ContactController::class);
Route::apiResource('/groups', App\Http\Controllers\Api\GroupController::class);
