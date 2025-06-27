<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\NewsletterCustomerController;
use App\Http\Controllers\PostCategoryController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostTopicController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RecipeCategoryController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RecipeDietController;
use App\Http\Controllers\RecipeUnitController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotas Públicas (Não exigem autenticação)
|--------------------------------------------------------------------------
*/

// Autenticação
Route::controller(AuthController::class)->prefix('auth')->group(function () {
    Route::post('/login', 'login');
    Route::post('/password/forgot', 'sendResetLink');
    Route::post('/password/reset', 'resetPassword');
});

// Autenticação Social (OAuth)
Route::controller(SocialAuthController::class)->prefix('auth/social')->group(function () {
    Route::get('/{provider}', 'redirectToProvider');
    Route::get('/{provider}/callback', 'handleProviderCallback');
});

// Registro de Usuário
Route::post('/users', [UserController::class, 'store'])->name('users.store');

// Contato e Newsletter
Route::post('/contact', [CustomerContactController::class, 'store']);
Route::post('/newsletter', [NewsletterCustomerController::class, 'store'])->name('newsletter.store');

// Categorias, Tópicos, Dietas e Unidades
Route::get('/post-categories', [PostCategoryController::class, 'index']);
Route::get('/post-categories/{postCategory}', [PostCategoryController::class, 'show']);
Route::get('/post-topics', [PostTopicController::class, 'index']);
Route::get('/post-topics/{postTopic}', [PostTopicController::class, 'show']);
Route::get('/recipe-categories', [RecipeCategoryController::class, 'index']);
Route::get('/recipe-categories/{recipeCategory}', [RecipeCategoryController::class, 'show']);
Route::get('/recipe-diets', [RecipeDietController::class, 'index']);
Route::get('/recipe-diets/{recipeDiet}', [RecipeDietController::class, 'show']);
Route::get('/recipe-units', [RecipeUnitController::class, 'index']);
Route::get('/recipe-units/{recipeUnit}', [RecipeUnitController::class, 'show']);

Route::get('/{type}/{commentable}/comments', [CommentController::class, 'index']);
Route::get('/comments/{comment}', [CommentController::class, 'show']);

Route::get('/{type}/{rateable}/ratings', [RatingController::class, 'index']);
Route::get('/ratings/{rating}', [RatingController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem autenticação via Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Usuários
    Route::apiResource('users', UserController::class)->except(['store']);
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/me', 'authUser');
        Route::patch('/{user}/role', 'updateRole');
        Route::post('/favorites/posts', 'toggleFavoritePost');
        Route::post('/favorites/recipes', 'toggleFavoriteRecipe');
    });

    // Posts e seus sub-recursos
    Route::apiResource('posts', PostController::class);
    Route::controller(PostController::class)->prefix('posts')->group(function () {
        Route::get('/my', 'userPosts');
        Route::get('/favorites', 'favorites');
    });

    // CRUDs de Categorias, Tópicos, etc. (exceto index/show)
    Route::apiResource('post-categories', PostCategoryController::class)->except(['index', 'show']);
    Route::apiResource('post-topics', PostTopicController::class)->except(['index', 'show']);
    Route::apiResource('recipe-categories', RecipeCategoryController::class)->except(['index', 'show']);
    Route::apiResource('recipe-diets', RecipeDietController::class)->except(['index', 'show']);
    Route::apiResource('recipe-units', RecipeUnitController::class)->except(['index', 'show']);

    // Receitas
    Route::apiResource('recipes', RecipeController::class);
    Route::get('recipes/my', [RecipeController::class, 'userRecipes']);
    Route::get('recipes/favorites', [RecipeController::class, 'favorites']);

    // Comentários
    Route::post('/{type}/{commentable}/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Avaliações
    Route::post('/{type}/{rateable}/ratings', [RatingController::class, 'store']);
    Route::put('/ratings/{rating}', [RatingController::class, 'update']);
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);

    // Contato e Newsletter (gerenciamento)
    Route::get('/contact', [CustomerContactController::class, 'index']);
    Route::get('/contact/{customer_contact}', [CustomerContactController::class, 'show']);
    Route::patch('/contact/{customer_contact}', [CustomerContactController::class, 'updateStatus']);
    Route::apiResource('newsletter', NewsletterCustomerController::class)->except(['store']);
});
