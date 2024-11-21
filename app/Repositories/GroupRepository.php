<?php

namespace App\Repositories;

use App\Models\Group;
use App\Models\Invitation;
use App\Models\UserGroup;

class GroupRepository{
    public function createGroup($data) {
        return Group::create($data);
    }
    
    public function inviteUser($data){
        return Invitation::create($data);
    }
    
    public function addToGroup($data){
        return UserGroup::create($data);
    }


    public function getGroup_byName($name) {
        return Group::where('name', '=', $name)
                    ->first();
    }
    
    public function getGroupUsers_byName($group_id, $page) {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        return UserGroup::where('group_id', '=', $group_id)
                        ->skip($offset)
                        ->take($limit)
                        ->pluck('user_id')
                        ->toArray();    
    }

    public function getInvitation_byGroupAndUser($group_id, $user_id){
        return Invitation::where('group_id', '=', $group_id)
                         ->where('user_id', '=', $user_id)
                         ->first();
    }

    public function getMember($user_id, $group_id){
        return UserGroup::where('user_id', '=', $user_id)
                        ->where('group_id', '=', $group_id)
                        ->first();
    }

    public function getInvitation_byId($id){
        return Invitation::where('id', '=', $id)
                         ->first();
    }

    public function deleteInvitation_byId($id){
        return Invitation::where('id', '=', $id)
                         ->delete();
    }

    function exitGroup($user_id, $group_id) {
        return UserGroup::where('user_id', '=', $user_id)
                        ->where('group_id', '=', $group_id)
                        ->delete();
    }
}
