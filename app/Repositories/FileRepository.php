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

    public function updateFileOnlytoChechIn($file_id, $data){  
        return File::where('id', '=', $file_id)
                   ->where('state','=', 0)
                   ->update($data);
    }
    
    public function updateFile($file_id, $data){  
        return File::where('id', '=', $file_id)
                   ->update($data);
    }

    function deleteCheck($check_id) {
        return FileCheck::where('id', '=', $check_id)
                        ->delete();
    }


    function getGroupFiles($group_id, $page) {
        $limit = 10;
        $offset = ($page - 1) * $limit;
        return File::where('group_id', '=', $group_id)
                   ->with('fileOwner:id,username')
                   ->skip($offset)
                    ->take($limit)
                   ->select(['id', 'file_name', 'state', 'owner_id', 'versions'])
                   ->get();
    }
    
    function deleteFile($file_id) {
        File::where('id', '=', $file_id)
            ->delete();
        FileCheck::where('file_id', '=', $file_id)
                 ->delete();
    }
    
    function viewFileDetails($file_id, $page) {
        $limit = 10;
        $offset = ($page - 1) * $limit;

        return History::where('file_id', '=', $file_id)
                      ->with('Users:id,username')
                      ->skip($offset)
                      ->take($limit)
                      ->select(['id', 'user_id','link', 'description'])
                      ->get();
    }
}