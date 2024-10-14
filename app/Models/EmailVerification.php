<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static updateOrCreate(array $attributes, array $values)
 * @method static where(string $string, string $token)
 */
class EmailVerification extends Model
{
    protected $fillable  = [
        'user_id',
        'token',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    use HasFactory;
}
