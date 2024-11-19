<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ServiceTransfromer;
use Illuminate\Support\Facades\Route;

class FileController extends Controller
{
    protected $service_transformer;
    
    function __construct(){
        $this->service_transformer = new ServiceTransfromer();
    }
    
    function uploadFiles(Request $request){
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'file uploaded successfully';

        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }
}
