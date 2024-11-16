<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository{
    public function create($data) {
        return User::create($data);
    }

    public function getAllUsers($page){
        $limit = 10;
        $offset = ($page - 1) * $limit;
        return User::skip($offset)->take($limit)->get();
    }
    

    public function getUser_byEmail($email){
        return User::where('email', '=', $email)
                   ->first();
    }

    public function getUser_byId($id){
        return User::where('id', '=', $id)
                   ->first();
    }


}