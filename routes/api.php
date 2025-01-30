<?php

use App\Http\Controllers\RecipeCategoryController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeDietController;
use App\Http\Controllers\RecipeIngredientController;
use App\Http\Controllers\RecipeStepController;
use App\Http\Controllers\RecipeUnitController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use \App\Http\Controllers\PostCategoryController;
use \App\Http\Controllers\UserController;

Route::apiResource('user', UserController::class);
Route::apiResource('posts', PostController::class);
Route::apiResource('post-categories', PostCategoryController::class);
Route::apiResource('recipes', RecipeController::class);
Route::apiResource('recipe-diets', RecipeDietController::class);
Route::apiResource('recipe-categories', RecipeCategoryController::class);
Route::apiResource('recipe-steps', RecipeStepController::class);
Route::apiResource('recipe-ingredients', RecipeIngredientController::class);
Route::apiResource('recipe-units', RecipeUnitController::class);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
