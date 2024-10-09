<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// class PasswordReset extends Model
// {
//     use HasFactory;

//     protected $fillable = [
//         'id',
//         'email',
//         'token',
//     ];
// }


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
