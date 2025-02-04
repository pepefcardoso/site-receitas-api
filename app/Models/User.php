<?php

namespace App\Models;

use App\Enum\RolesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $casts = [
        'role' => RolesEnum::class,
    ];

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
            'birthday' => 'required|date|before:today',
            'phone' => 'required|string|regex:/^\(\d{2}\)\s?\d{4,5}-\d{4}$/|unique:users,phone',
            'password' => 'required|string|min:8|max:99',
        ];
    }

    public static function updateRules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'email' => 'required|email|unique:users,email',
            'birthday' => 'required|date|before:today',
            'phone' => 'required|string|regex:/^\(\d{2}\)\s?\d{4,5}-\d{4}$/|unique:users,phone',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
    public static function loginRules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string',
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
}
