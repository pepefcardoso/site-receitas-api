<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterCustomer extends Model
{
    use HasFactory;

    protected $fillable = ['email'];

    public static function rules(): array
    {
        return [
            'email' => 'required|email|unique:newsletter_customers,email',
        ];
    }
}
