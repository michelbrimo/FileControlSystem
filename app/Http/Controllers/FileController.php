<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use App\Services\ServiceTransfromer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use App\Services\FileServices;



class FileController extends Controller
{
    protected $service_transformer;
    
    function __construct(){
        $this->service_transformer = new ServiceTransfromer();
    }
    
    function uploadFiles(Request $request, $group){
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = 'file uploaded successfully';

        if(is_string($group)){
            $request['group_name'] = $group;
        }else{
            $request['group_name'] = $group->name;
        }
        
        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function checkIn(Request $request) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "you've checked-in the file(s) successfully";

        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }
    
    public function checkOut(Request $request, $group_name, $file_id) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "you've checked-in the file(s) successfully";

        $request['file_id'] = $file_id;
        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }
    
    public function viewGroupFiles($group, $page=1) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "group files fetched successfully";

        return $this->service_transformer->execute(
            ['page'=> $page, 'group_id'=> $group->id],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }
    
    public function viewGroupFileDetails($group, $file_id, $page=1) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "file details fetched successfully";


        return $this->service_transformer->execute(
            ['file_id'=> $file_id, 'page'=> $page],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }

    public function deleteFile($group, $file_id) {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "file deleted successfully";

        return $this->service_transformer->execute(
            ['file_id'=> $file_id, 'group'=>$group],
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }


    public function compareFiles(Request $request)
    {
        $currentRoute = Route::current();
        $routeName = $currentRoute->getName();
        $service_function = $this->getRouteExploded($routeName);
        $success_message = "You've compared the files successfully";

        return $this->service_transformer->execute(
            $request->all(),
            $service_function['service'],
            $service_function['function'],
            $success_message
        );    
    }
    
}
