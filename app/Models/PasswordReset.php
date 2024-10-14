<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static updateOrCreate(array $attributes, array $values)
 * @method static where(string $string, string $email)
 */
class PasswordReset extends Model
{
    protected $fillable = ['email', 'token'];
    public $timestamps = false;
    protected $primaryKey = 'email';
    public $incrementing = false;
    protected $keyType = 'string';

    // Nếu bạn muốn sử dụng created_at nhưng không cần updated_at
    const UPDATED_AT = null;
}
