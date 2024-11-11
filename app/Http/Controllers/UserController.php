<?php

namespace App\Http\Controllers;

use App\Services\UserServices;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $user_services;
    
    function __construct(){
        $this->user_services = new UserServices();
    }

    public function register(Request $request) {
        return $this->user_services->createUser($request->all());
    }

}
