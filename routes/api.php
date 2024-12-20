<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use \App\Http\Controllers\PostCategoryController;
use \App\Http\Controllers\UserController;

Route::apiResource('user', UserController::class);
Route::apiResource('posts', PostController::class);
Route::apiResource('post-categories', PostCategoryController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
