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
}
