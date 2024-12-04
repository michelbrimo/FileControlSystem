<?php

namespace App\Repositories;

use App\Models\File;
use App\Models\FileCheck;
use App\Models\History;

class FileRepository{
    public function getFile_byId($file_id) {
        return File::where('id', '=', $file_id)
        ->first();
    }
    public function getFile_byName($file_name) {
        return File::where('file_name', '=', $file_name)
        ->first();
    }

    function createCheck($data) {
        return FileCheck::create($data);
    }

    function getCheckIn($file_id) {
        return FileCheck::where('file_id', '=', $file_id)
                        ->first();
    }
    
    public function createFile($data){  
        return File::create($data);
    }

    public function createHistory($data){  
        return History::create($data);
    }

    public function updateFile($file_id, $data){  
        return File::where('id', '=', $file_id)
                   ->where('state','=', 0)
                   ->update($data);
    }

    function deleteCheck($check_id) {
        return FileCheck::where('id', '=', $check_id)
                        ->delete();
    }

    

    
}