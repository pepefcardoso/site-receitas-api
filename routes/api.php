<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostCategoryController;
use App\Http\Controllers\PostTopicController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeCategoryController;
use App\Http\Controllers\RecipeDietController;
use App\Http\Controllers\RecipeIngredientController;
use App\Http\Controllers\RecipeStepController;
use App\Http\Controllers\RecipeUnitController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CustomerContactController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('users', UserController::class);
Route::get('user/me', [UserController::class, 'authUser']);
Route::get('users/roles', [UserController::class, 'listRoles']);
Route::post('user/role', [UserController::class, 'updateRole']);
Route::post('users/resetPassword', [UserController::class, 'resetPassword']);

/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/
// Place static routes before resource routes to avoid conflicts with implicit model binding.
Route::get('posts/my', [PostController::class, 'userPosts']);
Route::apiResource('posts', PostController::class);
Route::apiResource('post-categories', PostCategoryController::class);
Route::apiResource('post-topics', PostTopicController::class);

/*
|--------------------------------------------------------------------------
| Recipe Routes
|--------------------------------------------------------------------------
*/
Route::get('recipes/my', [RecipeController::class, 'userRecipes']);
Route::apiResource('recipes', RecipeController::class);
Route::apiResource('recipe-diets', RecipeDietController::class);
Route::apiResource('recipe-categories', RecipeCategoryController::class);
Route::apiResource('recipe-steps', RecipeStepController::class);
Route::apiResource('recipe-ingredients', RecipeIngredientController::class);
Route::apiResource('recipe-units', RecipeUnitController::class);

/*
|--------------------------------------------------------------------------
| Image Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('images', ImageController::class);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| Customer Contact Route
|--------------------------------------------------------------------------
*/
Route::post('/contact', [CustomerContactController::class, 'register']);
