<?php

namespace App\Services;

abstract class MainService{

    public $aspect_mapper = [
        'createGroup' => ['LoggingAspect'],
        'createUser' => ['LoggingAspect'],
    ];

    public function execute() {}
    
    public function executeBefore(array $aspects, $function, $rules=null, $data=null) {
        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->before($function, $rules, $data);
        }
    }

    public function executeAfter(array $aspects, $function) {
        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->after($function);
        }
    }

    public function executeException(array $aspects, $function) {
        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->exception($function);
        }
    }

    public function response($status, $message, $data=null){
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        
    }

}