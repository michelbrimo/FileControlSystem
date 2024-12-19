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
    
    public function uploadFiles(Request $request, $group){
        $groupName = is_string($group) ? $group : $group->name;
        $additionalData = ['group_name' => $groupName];
        $messege = 'File uploaded successfully';
        return $this->executeService($this->service_transformer, $request, $additionalData, $messege);
    }

    public function checkIn(Request $request, $group){
        $additionalData = ['group_id' => $group->id];
        $messege = "You've checked-in the file(s) successfully";
        return $this->executeService($this->service_transformer, $request, $additionalData, $messege);
    }

    public function checkOut(Request $request, $group, $file_id){
        $additionalData = ['group_id' => $group->id, 'file_id' => $file_id];
        $messege =  "You've checked-out the file(s) successfully";
        return $this->executeService($this->service_transformer, $request, $additionalData, $messege);
    }

    public function viewGroupFiles($group, $page = 1){
        $additionalData = ['group_id' => $group->id, 'page' => $page];
        $messege =  'Group files fetched successfully';
        return $this->executeService($this->service_transformer, new Request(),$additionalData ,$messege );
    }
    
    public function viewGroupFileDetails($group, $file_id, $page = 1){
        $additionalData = ['file_id' => $file_id, 'page' => $page];
        $messege =  'File details fetched successfully';
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }

    public function deleteFile($group, $file_id){
        $additionalData = ['group' => $group, 'file_id' => $file_id];
        $messege =  'File deleted successfully';
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }

    public function compareFiles(Request $request){
        $messege =  "You've compared the files successfully";
        return $this->executeService($this->service_transformer, $request, [],$messege);
    }

    public function seeChanges($group, $file_id){
        $additionalData = ['group' => $group, 'file_id' => $file_id];
        $messege =  "the changes is fetched successfully";
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }

    public function seeUserChanges($group, $file_id, $user_id){
        $additionalData = ['file_id' => $file_id, 'group' => $group, 'user_id' => $user_id];
        $messege =  "the changes is fetched successfully";
        return $this->executeService($this->service_transformer, new Request(), $additionalData, $messege);
    }
    
}



    

    


    

