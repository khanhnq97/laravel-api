<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface UserRepositoryInterface
{
    public function create(array $data);
    public function find(string $id);
    public function findByEmail(string $email);
    public function update(string $id, array $data);
}
