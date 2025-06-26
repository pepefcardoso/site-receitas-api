<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CustomerContactController;
use App\Http\Controllers\ImageController;
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

// Registro de Usuário (é o 'store' do UserController)
Route::post('/users', [UserController::class, 'store'])->name('users.store');

// Contato e Newsletter (geralmente públicos)
Route::post('/contact', [CustomerContactController::class, 'register']);
Route::post('/newsletter', [NewsletterCustomerController::class, 'store'])->name('newsletter.store');

// Categorias de Posts
Route::get('/post-categories', [PostCategoryController::class, 'index']);
Route::get('/post-categories/{postCategory}', [PostCategoryController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Rotas Protegidas (Exigem autenticação via Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Logout
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Usuários
    Route::controller(UserController::class)->prefix('users')->group(function () {
        Route::get('/me', 'authUser'); // Rota para obter o usuário logado
        Route::patch('/{user}/role', 'updateRole'); // Mais RESTful para atualizar a role
        Route::post('/favorites/posts', 'toggleFavoritePost'); // Agrupado sob 'favorites'
        Route::post('/favorites/recipes', 'toggleFavoriteRecipe');
    });
    Route::apiResource('users', UserController::class)->except(['store']); // 'store' já foi definido publicamente

    // Posts e seus sub-recursos
    Route::controller(PostController::class)->prefix('posts')->group(function () {
        Route::get('/my', 'userPosts');
        Route::get('/favorites', 'favorites');
    });
    Route::apiResource('posts', PostController::class);
    Route::apiResource('post-categories', PostCategoryController::class)
        ->except(['index', 'show']);
    Route::apiResource('post-topics', PostTopicController::class);

    // Receitas e seus sub-recursos
    Route::controller(RecipeController::class)->prefix('recipes')->group(function () {
        Route::get('/my', 'userRecipes');
        Route::get('/favorites', 'favorites');
    });
    Route::apiResource('recipes', RecipeController::class);
    Route::apiResource('recipe-categories', RecipeCategoryController::class);
    Route::apiResource('recipe-diets', RecipeDietController::class);
    Route::apiResource('recipe-units', RecipeUnitController::class);

    // Comentários (Aninhados e Polimórficos)
    Route::get('/{type}/{commentable}/comments', [CommentController::class, 'index'])->withoutMiddleware('auth:sanctum');
    Route::post('/{type}/{commentable}/comments', [CommentController::class, 'store']);
    Route::apiResource('comments', CommentController::class)->except(['index', 'store']);

    // Avaliações (Aninhadas e Polimórficas)
    Route::post('/{type}/{rateable}/ratings', [RatingController::class, 'store']);
    Route::put('/ratings/{rating}', [RatingController::class, 'update']);
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);

    // Contato e Newsletter (gerenciamento)
    Route::get('/contact', [CustomerContactController::class, 'index']);
    Route::get('/contact/{customer}', [CustomerContactController::class, 'show']);
    Route::patch('/contact/{contact}', [CustomerContactController::class, 'updateStatus']); // Mais RESTful
    Route::apiResource('newsletter', NewsletterCustomerController::class)->except(['store']);
});
