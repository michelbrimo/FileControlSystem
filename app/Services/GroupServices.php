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
        try {
            $rules = [
                'name' => 'required|string|unique:groups',
            ];

            $this->executeBefore(
                $this->aspect_mapper['createGroup'],
                __FUNCTION__,
                $rules,
                $data
            );
            
            $data['admin_id'] = auth()->user()->id;
            $result = $this->group_repository->create($data);

            $response = $this->response(
                true,
                'Group created successfully',
                $result
            );

            $this->executeAfter(
                $this->aspect_mapper['createGroup'],
                __FUNCTION__
            );            
        } catch (\Exception $e) {
            $message = $e->getMessage();   
            $response = $this->response(
                false,
                $message
            );
            
            $this->executeException(
                $this->aspect_mapper['createGroup'],
                __FUNCTION__,
            );
        }

        return $response;
    }
}
