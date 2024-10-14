<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use App\repositories\Interfaces\PasswordResetRepositoryInterface;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values)
    {
        return PasswordReset::updateOrCreate($attributes, $values);
    }

    public function findByEmailAndToken(string $email,string $token)
    {
        return PasswordReset::where('email', $email)
            ->where('token', $token)
            ->first();
    }

    public function deleteByEmail(string $email)
    {
        return PasswordReset::where('email', $email)->delete();
    }
}
