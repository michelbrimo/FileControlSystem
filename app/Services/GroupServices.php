<?php

namespace App\Services;

use Exception;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Validator;

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
        
        $result = $this->group_repository->createGroup($data);

        $this->group_repository->addToGroup([
            "group_id" => $result->id,
            "user_id" => $data['admin_id']]
        );
        return $result;
    }

    public function inviteUsers($data){
        $validator = Validator::make($data, [
            'users_id' => 'required|array',
            'group_name' => 'required|string'
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        } 

        $group_id = $this->group_repository->getGroup_byName($data['group_name'])->id;
        $data['admin_id'] = auth()->user()->id;
        $data['group_id'] = $group_id;

        $fixed_part = [
            'admin_id' => $data['admin_id'],
            'group_id' => $data['group_id']
        ];   

        $result = [];
        
        foreach ($data['users_id'] as $user_id){
            $invitation = $this->group_repository->getInvitation_byGroupAndUser($fixed_part['group_id'],  $user_id);
            $is_member = $this->group_repository->getMember($user_id, $group_id);
            if (!$invitation && !$is_member) 
                array_push(
                    $result,
                    $this->group_repository->inviteUser(array_merge($fixed_part, ['user_id' => (int)$user_id]))
                );
            
            else if($is_member)
                array_push(
                    $result,
                    array_merge($fixed_part, ['user_id' => (int)$user_id, 'message' => "this user is already in your group"])
                );
            
            else
                array_push(
                    $result,
                    array_merge($fixed_part, ['user_id' => (int)$user_id, 'message' => "you have already invited this user in ".$invitation->updated_at])
                );
            
        }
        return $result;
    }

    public function acceptInvitation($data){
        $validator = Validator::make($data, [
            'invitation_id' => 'required|integer',
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        }

        $invitation = $this->group_repository->getInvitation_byId($data['invitation_id']);
        if ($invitation) {
            $this->group_repository->addToGroup([
                "group_id" => $invitation->group_id,
                "user_id" => auth()->user()->id]
            );

            $this->group_repository->deleteInvitation_byId($data['invitation_id']);
            return null;
        }

        throw new Exception("Invitation doesn't exist", 400);
    }
    
    public function rejectInvitation($data){
        $validator = Validator::make($data, [
            'invitation_id' => 'required|integer',
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        }

        $invitation = $this->group_repository->getInvitation_byId($data['invitation_id']);
        if ($invitation) {
            $this->group_repository->deleteInvitation_byId($data['invitation_id']);
            return null;
        }

        throw new Exception("Invitation doesn't exist", 400);
    }

    public function viewGroupUsers($data) {
        $validator = Validator::make($data, [
            'page' => 'integer|required',
            'group_name' => 'string|required'
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        } 

        $group = $this->group_repository->getGroup_byName($data['group_name']);
        
        $result = $this->group_repository->getGroupUsers_byName($group->id, $data['page']);

        if ($result)
            return ["users_id" => $result];
    
        else
            throw new Exception('Group not found', 400);            
    }


}
