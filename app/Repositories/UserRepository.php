<?php

namespace App\Repositories;
use App\Models\User;

class UserRepository
{
    public function create(array $data):User
    {
        return User::create($data);
    }

    public function all($perPage = 10)
    {
        return User::with('posts')->paginate($perPage);
    }   

    public function findById($id)
    {
        return User::with('posts','bookmarks')->find($id);
    }

    public function update(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function allUsers()
    {
        return User::all();
    }

}