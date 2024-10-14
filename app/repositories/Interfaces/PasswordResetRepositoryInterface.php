<?php

namespace App\Repositories\Interfaces;

interface PasswordResetRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values);
    public function findByEmailAndToken(string $email,string $token);
    public function deleteByEmail(string $email);
}
