<?php

namespace App\Repositories;

use App\Models\EmailVerification;
use App\Repositories\Interfaces\EmailVerificationRepositoryInterface;

class EmailVerificationRepository implements EmailVerificationRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values)
    {
        return EmailVerification::updateOrCreate($attributes, $values);
    }

    public function findByToken($token)
    {
        return EmailVerification::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function delete($id)
    {
        return EmailVerification::destroy($id);
    }
}
