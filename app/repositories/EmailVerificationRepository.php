<?php

namespace App\Repositories;

use App\Models\EmailVerification;
use App\repositories\Interfaces\EmailVerificationRepositoryInterface;

class EmailVerificationRepository implements EmailVerificationRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values)
    {
        return EmailVerification::updateOrCreate($attributes, $values);
    }

    public function findByToken(string $token)
    {
        return EmailVerification::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();
    }

    public function delete(string $id): int
    {
        return EmailVerification::destroy($id);
    }
}
