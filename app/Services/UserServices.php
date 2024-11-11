<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserServices extends MainService
{
    protected $user_repository;
    protected $aspect;
    public function __construct() {
        $this->user_repository = new UserRepository();
    }

    public function createUser($data){
        $rules = [
            'username' => 'unique:users|string|required',
            'email' => 'unique:users|email|required',
            'password' => 'string|min:8|confirmed|required',
        ];

        try {
            $this->executeBefore(
                $this->aspect_mapper['createUser'],
                __FUNCTION__,
                $rules,
                $data
            );

            $result = $this->user_repository->create($data);
            $test = $result->username;
            $result['token'] = $result->createToken('personal access token')->plainTextToken;
            
            $response = $this->response(
                true,
                'User created successfully',
                $result
            );

            $this->executeAfter(
                $this->aspect_mapper['createUser'],
                __FUNCTION__
            );

        } catch (\Exception $e) {
            $message = $e->getMessage();   
            $response = $this->response(
                false,
                $message
            );
            
            $this->executeException(
                $this->aspect_mapper['createUser'],
                __FUNCTION__
            );
        }

        return $response;
    }
}
