<?php

namespace App\Services;

use Exception;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GroupServices
{
    protected $group_repository;
    protected $aspect;
    public function __construct() {
        $this->group_repository = new GroupRepository();
    }
    
    public function createGroup($data){
        $validator = Validator::make($data, [
            'name' => 'required|string|unique:groups',
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        } 
        
            
        $data['admin_id'] = auth()->user()->id;
        $result = $this->group_repository->create($data);
        return $result;
    }
}
