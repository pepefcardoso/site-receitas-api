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
Route::controller(AuthController::class)->prefix('auth')->middleware('throttle:auth')->group(function () {
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

// Categorias, Tópicos, Dietas e Unidades (Públicas)
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

// Rotas de POSTS que exigem autenticação.
Route::middleware('auth:sanctum')->controller(PostController::class)->prefix('posts')->group(function () {
    Route::get('/my', 'userPosts');
    Route::get('/favorites', 'favorites');
});

// Rotas públicas de POSTS
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

// Rotas de RECIPES que exigem autenticação
Route::middleware('auth:sanctum')->controller(RecipeController::class)->prefix('recipes')->group(function () {
    Route::get('/my', [RecipeController::class, 'userRecipes']);
    Route::get('/favorites', [RecipeController::class, 'favorites']);
});

// Rotas públicas de RECIPES
Route::get('/recipes', [RecipeController::class, 'index']);
Route::get('/recipes/{recipe}', [RecipeController::class, 'show']);


/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Restantes)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

    // Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Usuários
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/me', 'authUser');
        Route::patch('/{user}/role', 'updateRole');
        Route::post('/favorites/posts', 'toggleFavoritePost');
        Route::post('/favorites/recipes', 'toggleFavoriteRecipe');
    });
    Route::apiResource('users', UserController::class)->except(['store']);

    // Posts (Restantes - store, update, destroy)
    Route::apiResource('posts', PostController::class)->except(['index', 'show']);

    // CRUDs de Categorias, Tópicos, etc. (protegidos)
    Route::apiResource('post-categories', PostCategoryController::class)->except(['index', 'show']);
    Route::apiResource('post-topics', PostTopicController::class)->except(['index', 'show']);
    Route::apiResource('recipe-categories', RecipeCategoryController::class)->except(['index', 'show']);
    Route::apiResource('recipe-diets', RecipeDietController::class)->except(['index', 'show']);
    Route::apiResource('recipe-units', RecipeUnitController::class)->except(['index', 'show']);

    // Receitas
    Route::apiResource('recipes', RecipeController::class)->except(['index', 'show']);

    // Comentários
    Route::post('/{type}/{commentableId}/comments', [CommentController::class, 'store'])
        ->whereIn('type', ['posts', 'recipes'])
        ->whereNumber('commentableId');
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

    // Avaliações
    Route::get('/{type}/{rateableId}/rating', [RatingController::class, 'showUserRating'])
        ->whereIn('type', ['posts', 'recipes'])
        ->whereNumber('rateableId');
    Route::post('/{type}/{rateableId}/ratings', [RatingController::class, 'store'])
        ->whereIn('type', ['posts', 'recipes'])
        ->whereNumber('rateableId');
    Route::put('/ratings/{rating}', [RatingController::class, 'update']);
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);

    // Contato e Newsletter (gerenciamento)
    Route::get('/contact', [CustomerContactController::class, 'index']);
    Route::get('/contact/{customer_contact}', [CustomerContactController::class, 'show']);
    Route::patch('/contact/{customer_contact}', [CustomerContactController::class, 'updateStatus']);
    Route::apiResource('newsletter', NewsletterCustomerController::class)->except(['store']);
});

// Rotas públicas restantes para Comentários e Avaliações
Route::get('/{type}/{commentableId}/comments', [CommentController::class, 'index'])
    ->whereNumber('commentableId')
    ->whereIn('type', ['posts', 'recipes']);
Route::get('/comments/{comment}', [CommentController::class, 'show']);

Route::get('/{type}/{rateableId}/ratings', [RatingController::class, 'index'])
    ->whereIn('type', ['posts', 'recipes'])
    ->whereNumber('rateableId');
Route::get('/ratings/{rating}', [RatingController::class, 'show']);

// Rota para Health Check
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});
