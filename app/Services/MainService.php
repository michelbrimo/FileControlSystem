<?php

namespace App\Services;

abstract class MainService{

    public $aspect_mapper = [
        'createGroup' => ['testTransaction']
    ];

    public function execute() {}
    
    public function executeBefore(array $aspects) {
        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->before();
        }
    }

    public function executeAfter(array $aspects) {
        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->after();
        }
    }

    public function executeException(array $aspects) {
        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->exception();
        }
    }

    public function response($status, $message, $data=''){
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ]);
        
    }

}