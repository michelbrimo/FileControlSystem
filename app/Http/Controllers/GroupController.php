<?php

namespace App\Http\Controllers;

use App\Services\GroupServices;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    protected $group_services;
    
    function __construct(){
        $this->group_services = new GroupServices();
    }

    public function createGroup(Request $request) {
        return $this->group_services->createGroup($request->all());
    }
}
