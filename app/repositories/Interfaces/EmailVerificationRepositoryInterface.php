<?php

namespace App\Repositories\Interfaces;

interface EmailVerificationRepositoryInterface
{
    public function updateOrCreate(array $attributes, array $values);
    public function findByToken($token);
    public function delete($id);
}
