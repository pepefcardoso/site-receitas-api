<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\SocialAuthController;
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
use App\Http\Controllers\NewsletterCustomerController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('users', UserController::class);
Route::get('user/me', [UserController::class, 'authUser']);
Route::post('users/update-role', [UserController::class, 'updateRole']);
Route::post('users/favorite-post', [UserController::class, 'toggleFavoritePost']);
Route::post('users/favorite-recipe', [UserController::class, 'toggleFavoriteRecipe']);

/*
|--------------------------------------------------------------------------
| Post Routes
|--------------------------------------------------------------------------
*/
Route::get('posts/my', [PostController::class, 'userPosts']);
Route::get('posts/favorites', [PostController::class, 'favorites']);
Route::apiResource('posts', PostController::class);
Route::apiResource('post-categories', PostCategoryController::class);
Route::apiResource('post-topics', PostTopicController::class);

/*
|--------------------------------------------------------------------------
| Recipe Routes
|--------------------------------------------------------------------------
*/
Route::get('recipes/my', [RecipeController::class, 'userRecipes']);
Route::get('recipes/favorites', [RecipeController::class, 'favorites']);
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
| Ratings Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('ratings', RatingController::class);

/*
|--------------------------------------------------------------------------
| Comments Routes
|--------------------------------------------------------------------------
*/
Route::apiResource('comments', CommentController::class);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/password/forgot', [AuthController::class, 'sendResetLink'])->withoutMiddleware('auth:sanctum');
Route::post('/password/reset', [AuthController::class, 'resetPassword'])->withoutMiddleware('auth:sanctum');
Route::prefix('auth')->group(function () {
    Route::get('/{provider}', [SocialAuthController::class, 'redirectToProvider']);
    Route::get('/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback']);
});

/*
|--------------------------------------------------------------------------
| Customer Contact Route
|--------------------------------------------------------------------------
*/
Route::post('/contact', [CustomerContactController::class, 'register']);
Route::get('/contact', [CustomerContactController::class, 'index']);
Route::get('/contact/{customer}', [CustomerContactController::class, 'show']);
Route::post('/contact/update-status/{contactId}', [CustomerContactController::class, 'updateStatus']);
Route::apiResource('newsletter', NewsletterCustomerController::class);
