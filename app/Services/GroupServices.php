<?php

namespace App\Services;

use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GroupServices extends MainService
{
    protected $group_repository;
    protected $aspect;
    public function __construct() {
        $this->group_repository = new GroupRepository();
    }
    
    public function createGroup($data){
        $validator = Validator::make($data, [
            'name' => 'string|required' 
        ]);
        
        if ($validator->fails()) 
            throw new ValidationException($validator);


        try {
            $this->executeBefore(parent::$aspect_mapper['createGroup']);

            $data = $this->group_repository->create($data);
            $response = $this->response(
                true,
                'created successfully',
                $data
            );

            $this->executeAfter(parent::$aspect_mapper['createGroup']);
            
        } catch (\Exception $e) {
            $message = $e->getMessage();
            $response = $this->response(
                false,
                $message
            );
            
            $this->executeException(parent::$aspect_mapper['createGroup']);
        }

        return $response;
    }
    
}
