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

    public function createGroup(Request $request) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'group created successfully';
        
        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function inviteUsers(Request $request, $group) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'users invited successfully';
        
        $request['group'] = $group;
        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function acceptInvitation($invitation_id) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'invitation accepted successfully';
        
        return $this->service_transformer->execute(
            ['invitation_id' => $invitation_id],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    
    }
    public function rejectInvitation($invitation_id) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'invitation rejected successfully';
        
        return $this->service_transformer->execute(
            ['invitation_id' => $invitation_id],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function viewGroupUsers($group, $page=1) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "group's users has been fetched successfully";

        return $this->service_transformer->execute(
            ['page' => $page, 'group_id' => $group->id],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function exitGroup($group) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "you've exited the group successfully";

        return $this->service_transformer->execute(
            ['group_id' => $group->id],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function kickFromGroup($group, $user) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "you kicked this user successfully";

        return $this->service_transformer->execute(
            ['group_id' => $group->id, 'user_id' => $user->id],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function viewGroups(Request $request) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'group created successfully';
        
        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }
    
}
