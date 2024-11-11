<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class MainController extends Controller
{
    public function transform(Request $request){
        $route = Route::current();
        $route_name = $route->getName();
        $route_method = $request->method();

        $url_parameters = $route->parameters();
        $query_parameters = $request->all();

        $message = [];
        $message = $this->breakRouteName($message, $route_name);
        $message['method'] = $route_method;
        $message['request'] = $request;

        if(count($url_parameters) > 0){
            $message['url_parameters']=[];
            foreach($url_parameters as $key=>$value){
                $message['url_parameters'][$key]=$value;
            }
        }

        if(count($query_parameters) > 0){
            $message['query_parameters']=[];
            foreach($query_parameters as $key=>$value){
                $message['query_parameters'][$key]=$value;
            }
        }

        if(isset($route_method) && $route_method=="POST"){
            $body_paramaters = $request->all();
            $message['body_paramaters']=[];
            if(count($body_paramaters)>0){
                foreach($body_paramaters as $key=>$value){
                    $message['body_paramaters'][$key]=$value;
                }
            }
        }

        return response()->json();
    }

    public function breakRouteName($message, $routeName){
        $service_function = explode(".",$routeName);

        if(isset($service_function) && count($service_function)==2){
            $message["service"] = $service_function[0];
            $message["function"] = $service_function[1];
            return $message;
        }
        
        return null;
    }
}