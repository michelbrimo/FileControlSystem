<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getRouteExploded($routeName){
        $exp_arr=explode(".",$routeName);
        if(isset($exp_arr) && count($exp_arr)==2){
            $service_function["service"]=$exp_arr[0];
            $service_function["function"]=$exp_arr[1];
            return $service_function;
        }else{
            return null;
        }


}
}
