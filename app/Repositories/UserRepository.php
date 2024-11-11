<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository{
    public function create($data) {
        return User::create($data);
    }
}