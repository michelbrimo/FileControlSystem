<?php

namespace App\Aspects;

use Exception;
use App\Models\Log;
use Illuminate\Support\Facades\Validator;

class LoggingAspect
{   
    public function before($operation, $rules=null, $data=null){
        if ($rules != null){
            $validator = Validator::make($data, $rules);
            if($validator->fails()){
                throw new Exception(
                    $validator->errors()->first(),
                    422);
            }    
        }
        

        Log::create([
            "user_id" => auth()->user() != null ? auth()->user()->id : null,
            "username" => auth()->user() != null ? auth()->user()->username : 'unregistered user',
            "operation" => $operation,
            "status" => "Started",
        ]);
    }
    

    public function after($operation){
        Log::create([
            "user_id" => auth()->user() != null ? auth()->user()->id : null,
            "username" => auth()->user() != null ? auth()->user()->username : 'unregistered user',
            "operation" => $operation,
            "status" => "Finished",
        ]);
    }

    public function exception($operation){
        Log::create([
            "user_id" => auth()->user() != null ? auth()->user()->id : null,
            "username" => auth()->user() != null ? auth()->user()->username : 'unregistered user',
            "operation" => $operation,
            "status" => "Exception",
        ]);
    }

}
