<?php

namespace App\Models;

use App\Enum\RecipeDifficultyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Validation\Rule;
use Laravel\Scout\Searchable;

class Recipe extends Model
{
    use HasFactory, Searchable;

    public const VALID_SORT_COLUMNS = ['title', 'created_at', 'time', 'difficulty'];

    protected $fillable = [
        'title',
        'description',
        'time',
        'portion',
        'difficulty',
        'category_id',
        'user_id',
    ];

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    public function toSearchableArray(): array
    {
        $this->load('category', 'diets', 'user');

        return [
            'id'          => (int) $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'category'    => $this->category->name ?? null,
            'author'      => $this->user->name ?? null,

            'category_id'  => $this->category_id,
            'user_id'      => $this->user_id,
            'diets'        => $this->diets->pluck('id')->all(),
            'time'         => (int) $this->time,
            'difficulty'   => $this->difficulty,
            'created_at'   => $this->created_at->timestamp,
        ];
    }

    /**
     * Retorna os atributos que podem ser usados para filtrar no Meilisearch.
     *
     * @return array
     */
    public function filterableAttributes(): array
    {
        return ['category_id', 'user_id', 'diets'];
    }

    /**
     * Retorna os atributos que podem ser usados para ordenar no Meilisearch.
     *
     * @return array
     */
    public function sortableAttributes(): array
    {
        return ['created_at', 'title', 'time', 'difficulty'];
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['diets'])) {
            $query->whereHas('diets', function ($query) use ($filters) {
                $query->whereIn('recipe_diets.id', $filters['diets']);
            });
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function diets(): BelongsToMany
    {
        return $this->belongsToMany(RecipeDiet::class, 'rl_recipe_diets', 'recipe_id', 'recipe_diet_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RecipeCategory::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(RecipeStep::class)->orderBy('order');
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function ratings(): MorphMany
    {
        return $this->morphMany(Rating::class, 'rateable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'rl_user_favorite_recipes', 'recipe_id', 'user_id');
    }
}
