<?php

namespace App\Providers;

use App\Models\PostCategory;
use App\Models\PostTopic;
use App\Models\Recipe;
use App\Models\RecipeCategory;
use App\Models\RecipeDiet;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use App\Models\RecipeUnit;
use App\Models\User;
use App\Policies\PostCategoryPolicy;
use App\Policies\PostTopicPolicy;
use App\Policies\PostPolicy;
use App\Policies\RecipeCategoryPolicy;
use App\Policies\RecipeDietPolicy;
use App\Policies\RecipeIngredientPolicy;
use App\Policies\RecipePolicy;
use App\Policies\RecipeStepPolicy;
use App\Policies\RecipeUnitPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        RecipeDiet::class => RecipeDietPolicy::class,
        RecipeCategory::class => RecipeCategoryPolicy::class,
        RecipeUnit::class => RecipeUnitPolicy::class,
        RecipeIngredient::class => RecipeIngredientPolicy::class,
        RecipeStep::class => RecipeStepPolicy::class,
        Recipe::class => RecipePolicy::class,
        User::class => UserPolicy::class,
        Post::class => PostPolicy::class,
        PostCategory::class => PostCategoryPolicy::class,
        PostTopic::class => PostTopicPolicy::class,
    ];

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
