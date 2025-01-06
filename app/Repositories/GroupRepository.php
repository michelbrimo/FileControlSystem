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
        $group = $this->getGroup_byId($data['group_id']);
        $this->updateGroup($group->id, ['numberOfMembers' => $group->numberOfMembers +1]);

        return UserGroup::create($data);
    }

    public function updateGroup($id, $data) {
        return Group::where('id', '=', $id)
                    ->update($data);
    }
    
    public function deleteGroup($id) {
        return Group::where('id', '=', $id)
                    ->delete();
    }

    public function getGroup_byName($name) {
        return Group::where('name', '=', $name)
                    ->first();
    }
    
    public function getGroup_byId($id) {
        return Group::where('id', '=', $id)
                    ->first();
    }
    
    public function getGroupUsers_byName($group_id, $page) {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $users = UserGroup::where('group_id', '=', $group_id)
                        ->join('users', 'user_groups.user_id', '=', 'users.id') 
                        ->skip($offset)
                        ->take($limit)
                        ->select('users.id', 'users.username') 
                        ->get()
                        ->toArray();
        $admin = Group::where('id', '=', $group_id)
                ->with(['admin:id,username'])
                ->select('admin_id')
                ->get();

        return ['users' => $users, 'admin' => $admin];
    }

    public function getGroups($page,){
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $userId = auth()->user()->id;
    
        $groups = Group::select('id', 'name', 'admin_id', 'numberOfMembers', 'numberOfFiles')
                       ->with(['admin:id,username']) // Load only the admin's id and username
                       ->skip($offset)
                       ->take($limit)
                       ->get();
                           
        $groups->each(function($group) use ($userId) {
            $isMember = UserGroup::where('user_id', $userId)
                                 ->where('group_id', $group->id)
                                 ->exists();
    
            $group->isMember = $isMember;
        });
    
        
        return $groups;
    }
        
    public function getMyGroups($page){
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        return Group::join('user_groups', 'user_groups.group_id', '=', 'groups.id')
                    ->where('user_groups.user_id', '=', auth()->user()->id)
                    ->with(['admin:id,username']) // Load only the admin's id and username
                    ->select('groups.id', 'groups.name', 'groups.admin_id', 'groups.numberOfMembers', 'groups.numberOfFiles') 
                    ->get()
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

    public function getMyInvitations($user_id){
        return Invitation::where('user_id', '=', $user_id)
                         ->with(['group:id,name'])  
                         ->with(['admin:id,username'])
                         ->select(['id','group_id', 'admin_id'])
                         ->get();
    }

    function exitGroup($user_id, $group_id) {
        return UserGroup::where('user_id', '=', $user_id)
                        ->where('group_id', '=', $group_id)
                        ->delete();
    }
}
