<?php

namespace App\Services;

use Exception;
use App\Repositories\FileRepository;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileServices 
{
    protected $file_repository;
    protected $group_repository;
    protected $aspect;
    public function __construct() {
        $this->file_repository = new FileRepository();
        $this->group_repository = new GroupRepository();
    }


    public function uploadFiles($data){
        $validator = Validator::make($data, [
            'file_path' => 'required|array', 
            'group_name' => 'string',
            'file_id' => 'integer'
        ]);
    
        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }
        
        $group = $this->group_repository->getGroup_byName($data['group_name']); 
        if (!$group) throw new Exception('Group not found.', 404);
        

        $results = []; 
        foreach ($data['file_path'] as $filePath) {
            if (!file_exists($filePath)) throw new Exception($filePath." does not exist.", 404);

            $result = $this->processSingleFile([
                'file_path' => $filePath,
                'group_id' => $group->id,
                'file_id' => $data['file_id'] ?? null
            ]);
            $results[] = $result;
        }
        return $results; 
    }

    private function processSingleFile($data)
    {
        $originalFilePath = $data['file_path'];

        $fileName = basename($originalFilePath); 
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME); 
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); 
        $uniqueFileName = $nameWithoutExtension . '_' . $data['group_id'];

        if($data['file_id']){
            $file = $this->file_repository->getFile_byId($data['file_id']);
            if($file->state == 1){
                $file_check = $this->file_repository->getLastFileCheckIn_byId($data['file_id']);
                if($file_check->user_id != auth()->user()->id)
                    throw new Exception("the file is already checked-in by another user.", 400);
            }
            else 
                throw new Exception("please check-in the file then upload it.", 400);

            if($fileName != $file->file_name)
                throw new Exception("your file name doesn't match with the group file name.", 400);
            $storagePath = 'files/' . $uniqueFileName . '/' . $nameWithoutExtension . "_v2." . $fileExtension;
        }
        else{
            if($this->file_repository->getFile_byName($fileName))
                throw new Exception($uniqueFileName." already exists in your group.", 400);
            $storagePath = 'files/' . $uniqueFileName . '/' . $nameWithoutExtension . "_original." .  $fileExtension;
        }
            

        
        $fileContents = file_get_contents($originalFilePath);
        Storage::disk('public')->put($storagePath, $fileContents);

        if($data['file_id']){
            $this->file_repository->updateFile($data['file_id'], [
                'state' => 0,
                'file_path' => $storagePath
            ]);

            $this->file_repository->createCheck([
                'file_id' => $data['file_id'],
                'user_id' => auth()->user()->id,
                'checks' => 0
            ]);

            return $this->file_repository->uploadExistingFiles([
                'link' => $storagePath,
                'user_id' => auth()->user()->id,
                'file_id' => $data['file_id']
            ]); 
        }
        else 
            return $this->file_repository->uploadNewFiles([
                'state' => 0,
                'file_name' => $fileName,
                'file_path' => $storagePath,
                'group_id' => $data['group_id']
            ]); 
        
    }
        
}