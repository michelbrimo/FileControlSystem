<?php

namespace App\Repositories;

use App\Models\File;

class FileRepository{
    public function uploadFiles($data){  
        return File::create($data);
    }
}