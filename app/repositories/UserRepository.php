<?php

namespace App\Repositories;

use App\repositories\Interfaces\UserRepositoryInterface;
use App\Models\User;

/**
 * Class UserRepository
 * @package App\Repositories
 */
class UserRepository implements UserRepositoryInterface
{
    public function create(array $data)
    {
        return User::create($data);
    }

    public function find(string $id)
    {
        return User::findOrFail($id);
    }

    public function findByEmail(string $email)
    {
        return User::where('email', $email)->first();
    }

    public function update(string $id, array $data)
    {
        $user = $this->find($id);
        $user->update($data);
        return $user;
    }
}
