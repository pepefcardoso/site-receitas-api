<?php

namespace App\Models;

use App\Enum\RolesEnum;
use App\Notifications\PasswordResetNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\Rule;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'birthday',
        'phone',
        'password',
        'role',
        'provider',
        'provider_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => RolesEnum::class,
    ];

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new PasswordResetNotification($this->name, $token));
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';

            $query->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', $searchTerm)
                    ->orWhere('email', 'like', $searchTerm)
                    ->orWhere('phone', 'like', $searchTerm)
                    ->orWhere('id', 'like', $searchTerm);
            });
        }

        if (!empty($filters['role']) && is_array($filters['role'])) {
            $query->whereIn('role', $filters['role']);
        }

        if (!empty($filters['birthday_start']) && !empty($filters['birthday_end'])) {
            $query->whereBetween('birthday', [$filters['birthday_start'], $filters['birthday_end']]);
        }

        return $query;
    }


    public static function createRules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|regex:/^\d{10,11}$/|unique:users,phone',
            'password' => 'required|string|min:8|max:99',
            'confirm_password' => 'required|string|same:password',
        ];
    }

    public static function updateRules($userId = null): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'birthday' => 'nullable|date|before:today',
            'phone' => 'required|string|regex:/^\d{10,11}$/|unique:users,phone,' . $userId,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'password' => 'nullable|string|min:8|max:99',
            'confirm_password' => 'nullable|string|same:password',
        ];
    }

    public static function loginRules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
        ];
    }

    public static function setRoleRules(): array
    {
        $roleValues = array_map(fn($case) => $case->value, RolesEnum::cases());

        return [
            'user_id' => 'required|exists:users,id',
            'role' => ['required', Rule::in($roleValues)]
        ];
    }

    public static function resetPasswordRules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
        ];
    }

    public function isInternal(): bool
    {
        return $this->role->value <= RolesEnum::INTERNAL->value;
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(Image::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    public function favoritePosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'rl_user_favorite_posts', 'user_id', 'post_id');
    }

    public function favoriteRecipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'rl_user_favorite_recipes', 'user_id', 'recipe_id');
    }
}
