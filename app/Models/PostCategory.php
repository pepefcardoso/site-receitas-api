<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostCategory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['name'];

    public static array $rules = [
        'name' => 'required|string|max:50|unique:post_categories',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'rl_post_categories', 'post_category_id', 'post_id');
    }
}
