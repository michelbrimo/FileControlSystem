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

    public function register(Request $request) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'user created successfully';
        
        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );
    }

    public function login(Request $request) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'user logged successfully';

        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );
    }

    public function myProfile() {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "user's profile fetched successfully";

        return $this->service_transformer->execute(
            ['id' => auth()->user()->id],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

}
