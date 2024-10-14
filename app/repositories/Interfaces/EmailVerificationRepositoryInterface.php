<?php

namespace App\Repositories\Interfaces;

interface EmailVerificationRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values);
    public function findByToken(string $token);
    public function delete(string $id);
}
