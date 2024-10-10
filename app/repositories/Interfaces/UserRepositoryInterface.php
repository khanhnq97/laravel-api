<?php

namespace App\Repositories\Interfaces;

/**
 * Interface UserRepositoryInterface
 * @package App\Repositories\Interfaces
 */
interface UserRepositoryInterface
{
    public function create(array $data);
    public function find($id);
    public function findByEmail($email);
    public function update($id, array $data);
}
