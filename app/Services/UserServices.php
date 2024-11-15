<?php

namespace App\Services;

use Exception;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserServices
{
    protected $user_repository;

    public function __construct() {
        $this->user_repository = new UserRepository();
    }

    public function createUser($data){
        $validator = Validator::make($data, [
            'username' => 'unique:users|string|required',
            'email' => 'unique:users|email|required',
            'password' => 'string|min:8|confirmed|required',
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        }  
        
        $result = $this->user_repository->create($data);
        $result['token'] = $result->createToken('personal access token')->plainTextToken;
            
        return $result;
    }

    public function login($data) {
        $validator = Validator::make($data, [
            'email' => 'email|required',
            'password' => 'string|required',
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        } 
           

        $result = $this->user_repository->getUser_byEmail($data['email']);

        if ($result && Hash::check($data['password'], $result->password)) {
            $result['token'] = $result->createToken('personal access token')->plainTextToken;
            return $result;
        }

        else
            throw new Exception("Email or Password are incorrect", 400);
        
    }

    public function getUserProfile($data) {
        $validator = Validator::make($data, [
            'id' => 'integer|required'
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        } 
        
        $result = $this->user_repository->getUser_byId($data['id']);

        if ($result)
            return $result;
    
        else
            throw new Exception('User not found', 400);            
    }
}
