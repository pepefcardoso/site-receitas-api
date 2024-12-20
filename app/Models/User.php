<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes;

    public mixed $role;
    public mixed $id;

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

    public static array $createRules = [
        'name' => 'required|string|min:3|max:100',
        'email' => 'required|email|unique:users,email',
        'birthday' => 'required|date|before:today',
        'phone' => 'required|string|regex:/^\(\d{2}\)\s?\d{4,5}-\d{4}$/|unique:users,phone',
        'password' => 'required|string|min:8|max:99',
    ];

    public static array $updateRules = [
        'name' => 'required|string|min:3|max:100',
        'email' => 'required|email|unique:users,email',
        'birthday' => 'required|date|before:today',
        'phone' => 'required|string|regex:/^\(\d{2}\)\s?\d{4,5}-\d{4}$/|unique:users,phone',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }
}
