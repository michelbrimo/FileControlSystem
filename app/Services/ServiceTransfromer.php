<?php

namespace App\Services;

use Exception;

class ServiceTransfromer{

    public $aspect_mapper = [
        'createUser' => ['LoggingAspect'],
        'login' => [],
        'getUserProfile' => [],
        'viewUsers' => [],
        'viewGroupUsers' => [],
        'inviteUsers' => ['LoggingAspect'],
        'acceptInvitation' => ['LoggingAspect', 'TransactionAspect'],
        'rejectInvitation' => ['LoggingAspect', 'TransactionAspect'],
        'createGroup' => ['LoggingAspect'],
    ];

    private $service_mapper = [];
    protected $userService;
    protected $groupService;

    public function __construct()
    {
        $this->userService = new UserServices();
        $this->groupService = new GroupServices();

        $this->service_mapper = [
            "Users" => "App\\Services\\UserServices",
            "Groups" => "App\\Services\\GroupServices",
        ];
    }

    public function execute($data, $service, $function_name, $success_message) {
        try{
            $this->executeBefore($function_name);

            $service_obj = new $this->service_mapper[$service];
            $result = $service_obj->$function_name($data);

            $response = $this->response(
                true,
                $success_message,
                200,
                $result
            );

            $this->executeAfter($function_name);
        }
        catch(Exception $e){
            $response = $this->response(
                false,
                $e->getMessage(),
                $e->getCode(),
                null,
            );
            
            $this->executeException($function_name);
        }

        return $response;
    }
    
    public function executeBefore($function_name) {
        $aspects = $this->aspect_mapper[$function_name];
        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->before($function_name);
        }
    }

    public function executeAfter($function_name) {
        $aspects = $this->aspect_mapper[$function_name];

        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->after($function_name);
        }
    }

    public function executeException($function_name) {
        $aspects = $this->aspect_mapper[$function_name];

        foreach ($aspects as $aspect) {
            $object = 'App\\Aspects\\'. $aspect;
            $class = new $object();
            $class->exception($function_name);
        }
    }

    public function response($status, $message, $code=200, $data){
        return response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data
        ], $code);
        
    }

}