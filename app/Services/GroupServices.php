<?php

namespace App\Services;

use Exception;
use App\Models\Group;
use Illuminate\Support\Facades\Gate;
use App\Repositories\GroupRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Validator;

class GroupServices
{
    protected $group_repository;
    protected $user_repository;
    protected $aspect;
    public function __construct() {
        $this->group_repository = new GroupRepository();
        $this->user_repository = new UserRepository();
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
            'group' => 'required'
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        } 
        
        $fixed_part = [
            'admin_id' => $data['group']->admin_id,
            'group_id' => $data['group']->id
        ];   

        $result = [];
        
        foreach ($data['users_id'] as $user_id){
            $invitation = $this->group_repository->getInvitation_byGroupAndUser($fixed_part['group_id'],  $user_id);
            $is_member = $this->group_repository->getMember($user_id, $data['group']->id);
            if (!$invitation && !$is_member) 
                $result [] = $this->group_repository->inviteUser(array_merge($fixed_part, ['user_id' => (int)$user_id]));
            
            else if($is_member)
                $result [] = array_merge(
                    $fixed_part,
                    [
                        'user_id' => (int)$user_id,
                        'message' => "this user is already in your group"
                    ]
                );
                

            else
                $result [] = array_merge(
                    $fixed_part,
                    [
                        'user_id' => (int)$user_id,
                        'message' => "you have already invited this user in ".$invitation->updated_at
                    ]
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

    public function viewMyInvitations($data) {
        $invitations = $this->group_repository->getMyInvitations(auth()->user()->id);
        return $invitations;
    }

    public function viewGroupUsers($data) {
        $validator = Validator::make($data, [
            'page' => 'integer|required',
            'group_id' => 'required'
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        } 

        $result = $this->group_repository->getGroupUsers_byName($data['group_id'], $data['page']);
        return ["users" => $result];
    }

    function exitGroup($data) {
        $validator = Validator::make($data, [
            'group' => 'required'
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        }

        $this->group_repository->exitGroup(auth()->user()->id, $data['group']->id);
    
        if ($data['group']->numberOfMembers == 1)
            $this->group_repository->deleteGroup($data['group']->id);
        else
            $this->group_repository->updateGroup($data['group']->id, ["numberOfMembers" => $data['group']->numberOfMembers -1]);


        return null;
    }

    function kickFromGroup($data) {
        $validator = Validator::make($data, [
            'group' => 'required',
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            throw new Exception(
                $validator->errors()->first(),
                422);
        }

        $is_member = $this->group_repository->getMember($data['user_id'], $data['group']->id);

        if ($is_member){
            $this->group_repository->exitGroup($data['user_id'], $data['group']->id);
            $this->group_repository->updateGroup($data['group']->id, ["numberOfMembers" => $data['group']->numberOfMembers -1]);
            return null;
        }
        else throw new Exception("user is not a member in this group", 400);
    }

    function viewGroups($data){
        return $this->group_repository->getGroups($data['page']);
    }
    
    function viewMyGroups($data){
        return $this->group_repository->getMyGroups($data['page']);
    }
    


}
