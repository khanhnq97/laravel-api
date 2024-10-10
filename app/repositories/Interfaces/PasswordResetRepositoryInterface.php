<?php

namespace App\Repositories\Interfaces;

interface PasswordResetRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values);
    public function findByEmailAndToken($email, $token);
    public function deleteByEmail($email);
}
