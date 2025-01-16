<?php

namespace App\Http\Controllers;

use App\Services\ServiceTransfromer;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;


class UserController extends Controller
{
    protected $service_transformer;
    
    function __construct(){
        $this->service_transformer = new ServiceTransfromer();
    }
    
    public function register(Request $request){
        return $this->executeService($this->service_transformer, $request, [], 'User registered successfully');
    }
    public function login(Request $request){
        return $this->executeService($this->service_transformer, $request, [], 'User logged in successfully');
    }
    public function myProfile(){
        $additionalData = ['id' => auth()->user()->id];
        return $this->executeService($this->service_transformer, new Request(), $additionalData, "User's profile fetched successfully");
    }
    public function viewUsers($page = 1){
        $additionalData = ['page' => $page];
        return $this->executeService($this->service_transformer, new Request(), $additionalData, 'Users fetched successfully');
    }
    public function tracing($user_id, $page = 1){
        $additionalData = ['page' => $page, 'user_id' => $user_id];
        return $this->executeService($this->service_transformer, new Request(), $additionalData, 'User traced successfully');
    }

}