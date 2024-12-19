<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GroupServices;
use App\Services\ServiceTransfromer;
use Illuminate\Support\Facades\Route;

class GroupController extends Controller
{
    protected $service_transformer;
    
    function __construct(){
        $this->service_transformer = new ServiceTransfromer();
    }
    
    public function createGroup(Request $request){
        $messege = 'Group created successfully';
        return $this->executeService($this->service_transformer, $request, [], $messege);
    }
    public function inviteUsers(Request $request, $group){
        $additionalData = ['group' => $group];
        $messege =  'Users invited successfully';
        return $this->executeService($this->service_transformer, $request, $additionalData, $messege);
    }
    public function acceptInvitation($invitation_id){
        $additionalData = ['invitation_id' => $invitation_id];
        $messege =  'Invitation accepted successfully';
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
    public function rejectInvitation($invitation_id){
        $additionalData = ['invitation_id' => $invitation_id];
        $messege =  'Invitation rejected successfully';
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
    public function viewMyInvitations($page = 1){
        $additionalData = ['page' => $page];
        $messege =  'Invitation fetched successfully';
        return $this->executeService($this->service_transformer, new Request(),$additionalData, $messege);
    }
    public function viewGroupUsers($group, $page = 1){
        $additionalData = ['group_id' => $group->id, 'page' => $page];
        $messege =  "Group's users fetched successfully";
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
    public function exitGroup($group){
        $additionalData = ['group' => $group];
        $messege =   "You've exited the group successfully";
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
    public function kickFromGroup($group, $user){
        $additionalData =  ['group' => $group, 'user_id' => $user->id];
        $messege =  'User kicked from group successfully';
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
    public function viewGroups($page = 1){
        $additionalData = ['page' => $page];
        $messege =   'Groups fetched successfully';
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
    public function viewMyGroups($page = 1){
        $additionalData = ['page' => $page];
        $messege =   "your groups fetched successfully";
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
}
