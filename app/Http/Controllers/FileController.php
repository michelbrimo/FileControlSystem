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

        $request['group_id'] = $group->id;
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




    # testing
    public function diff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_path' => 'required',
            'new_path' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $old_path = $request->old_path;
        $new_path = $request->new_path;

        try {
            $result = FileServices::compareFiles($old_path, $new_path);

            if (isset($result['message'])) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'diff' => $result['diff'],
                ]);
            }

            return response()->json([
                'success' => true,
                'diff' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
