<?php

namespace App\Aspects;
use App\Models\Log;

class FileLoggingAspect{
    public function before($operation, $rules=null, $data=null){
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