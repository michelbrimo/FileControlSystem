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
            'file_path.*' => 'required|string',
            'group_name' => 'string'
        ]);
    
        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }
    
        
        $group = $this->group_repository->getGroup_byName($data['group_name']); 
        if (!$group) {
            throw new Exception('Group not found.', 404);
        }
    
        $results = []; 
        foreach ($data['file_path'] as $filePath) {
            $fileData = [
                'file_path' => $filePath,
                'group_id' => $group->id,
            ];
    
            try {
                $result = $this->processSingleFile($fileData);
                $results[] = $result;
            } catch (Exception $e) {
                $results[] = [
                    'file_path' => $filePath,
                    'error' => $e->getMessage()
                ];
            }
        }
        return $results; 
    }

    private function processSingleFile($data)
    {
        $originalFilePath = $data['file_path'];
        $fileName = basename($originalFilePath); 
        
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); 
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME); 
        $uniqueFileName = $nameWithoutExtension . '_' . auth()->user()->id . '.' . $fileExtension;
        
        $storagePath = 'files/original/' . $uniqueFileName;

        
        if (!file_exists($originalFilePath)) {
            throw new Exception('File does not exist at the provided path.', 404);
        }

        
        $fileContents = file_get_contents($originalFilePath);
        Storage::disk('public')->put($storagePath, $fileContents);

        
        $data['state'] = 0; 
        $data['file_name'] = $uniqueFileName;
        $data['file_path'] = $storagePath;

        return $this->file_repository->uploadFiles($data);
    }
        
}