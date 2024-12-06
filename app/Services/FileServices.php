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
            'file_path' => 'required', 
            'group_name' => 'required',
        ]);
    
        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }
    
        $group_id = $this->group_repository->getGroup_byName($data['group_name'])->id;

        $results = []; 
        foreach ($data['file_path'] as $filePath) {
            if (!file_exists($filePath)) throw new Exception($filePath." does not exist.", 404);

            $result = $this->processSingleFile([
                'file_path' => $filePath,
                'group_id' => $group_id,
            ]);
            $results[] = $result;
        }
        return $results; 
    }

    private function processSingleFile($data)
    {
        $originalFilePath = $data['file_path'];
    
        $fileName = $originalFilePath->getClientOriginalName(); 
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME); 
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); 
        $uniqueFileName = $nameWithoutExtension . '_' . $data['group_id'];
    
       
        if ($this->file_repository->fileExists($fileName, $data['group_id'])) {
            throw new Exception($uniqueFileName . " already exists in your group.", 400);
        }
    
        $storagePath = 'files/' . $uniqueFileName . '/' . $nameWithoutExtension . "_original." . $fileExtension;
        $fileContents = file_get_contents($originalFilePath);
    
        $file = $this->file_repository->createFile([
            'state' => 0,
            'file_name' => $fileName,
            'file_path' => 'storage/'.$storagePath,
            'group_id' => $data['group_id'],
            'owner_id' => auth()->user()->id,
            'versions' => 1
        ]); 
    
        $this->file_repository->createHistory([
            'link' => $file->file_path,
            'user_id' => auth()->user()->id,
            'file_id' => $file->id,
            'description' => "initial commit"
        ]);
    
        $group = $this->group_repository->getGroup_byId($data['group_id']);
        $this->group_repository->updateGroup($group->id, ['numberOfFiles' => $group->numberOfFiles + 1]);
    
        Storage::disk('public')->put($storagePath, $fileContents);
            return [
            'file' => $file,
            'file_contents' => $fileContents
        ];
    }
    

    public function checkIn($data){
        $validator = Validator::make($data, [
            'files_id' => 'required|array', 
        ]);
    
        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }
        
        $results = []; 
        foreach ($data['files_id'] as $file_id) {
            $check_in = $this->file_repository->getCheckIn($file_id);
            if ($check_in){
                $file = $this->file_repository->getFile_byId($check_in['file_id']);
                throw new Exception("$file->file_name is checked-in by another user", 400);
            }
            
            $this->file_repository->updateFileOnlytoChechIn($file_id, ["state" => 1]);
            
            $results[] = $this->file_repository->createCheck([
                'user_id' => auth()->user()->id,
                'file_id' => $file_id
            ]);
        }

        return $results; 
    }

    function checkOut($data) {
        $validator = Validator::make($data, [
            'file_path' => 'required|file', 
            'file_id' => 'required|integer',
            "description" => 'string'
        ]);

        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }

        $file = $this->file_repository->getFile_byId($data['file_id']);

        $originalFilePath = $data['file_path'];

        $fileName = $originalFilePath->getClientOriginalName(); 
        $nameWithoutExtension = pathinfo($fileName, PATHINFO_FILENAME); 
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); 
        $uniqueFileName = $nameWithoutExtension . '_' . $file->group_id;


        $check_in = $this->file_repository->getCheckIn($data['file_id']);

        if (!$check_in)
            throw new Exception("please check-in the file then upload it.", 400);

        if ($check_in && auth()->user()->id != $check_in->user_id)
            throw new Exception("$file->file_name is checked-in by another user", 400);
        
        if($fileName != $file->file_name)
            throw new Exception("your file name doesn't match with the group file name.", 400);


        
        $storagePath = "files/$uniqueFileName/$nameWithoutExtension"."_v".$file->versions+1 .".$fileExtension";
        $fileContents = file_get_contents($originalFilePath);
        Storage::disk('public')->put($storagePath, $fileContents);

        $this->file_repository->updateFile($data['file_id'], [
            'state' => 0,
            'file_path' => $storagePath,
            'versions' => $file->versions +1
        ]);

        $this->file_repository->deleteCheck([
            'check_id' => $check_in->id,
        ]);

        return $this->file_repository->createHistory([
            'link' => $storagePath,
            'user_id' => auth()->user()->id,
            'file_id' => $data['file_id'],
            'description' => $data['description']
        ]); 
    }
    
    public function viewGroupFiles($data) {
        $validator = Validator::make($data, [
            'page' => 'integer', 
            'group_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }

        return $this->file_repository->getGroupFiles($data['group_id'], $data['page']);
    }
    
    public function deleteFile($data) {
        $validator = Validator::make($data, [
            'file_id' => 'integer|required', 
            'group' => 'required', 
        ]);

        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }

        $this->group_repository->updateGroup($data['group']->id, ["numberOfFiles" => $data['group']->numberOfFiles -1]);
        return $this->file_repository->deleteFile($data['file_id']);
    }
    
    public function viewGroupFileDetails($data) {
        $validator = Validator::make($data, [
            'file_id' => 'integer|required', 
            'page' => 'integer', 
        ]);

        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }

        return $this->file_repository->viewFileDetails($data['file_id'], $data['page']);
    }

    public function compareFiles($data)
    {
        $validator = Validator::make($data, [
            'old_path' => 'required|string', 
            'new_path' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        $oldFilePath = $data['old_path'];
        $newFilePath = $data['new_path'];

        if (!file_exists($oldFilePath) || !file_exists($newFilePath)) {
            return response()->json([
                'success' => false,
                'message' => "One or both files do not exist."
            ], 400);
        }

        $oldLines = file($oldFilePath, FILE_IGNORE_NEW_LINES);
        $newLines = file($newFilePath, FILE_IGNORE_NEW_LINES);

        $diffrence = [];
        $maxLines = max(count($oldLines), count($newLines));

        for ($i = 0; $i < $maxLines; $i++) {
            $oldLine = $oldLines[$i] ?? null;
            $newLine = $newLines[$i] ?? null;

            if (is_null($oldLine)) {
                $diffrence[] = [
                    'type' => 'added',
                    'line' => $i + 1,
                    'content' => $newLine,
                ];
            } elseif (is_null($newLine)) {
                $diffrence[] = [
                    'type' => 'removed',
                    'line' => $i + 1,
                    'content' => $oldLine,
                ];
            } elseif ($oldLine !== $newLine) {
                $diffrence[] = [
                    'type' => 'modified',
                    'line' => $i + 1,
                    'old_content' => $oldLine,
                    'new_content' => $newLine,
                    'changes' => $this->highlightChanges($oldLine, $newLine),
                ];
            }
        }

        if (empty($diffrence)) {
            return response()->json([
                'success' => true,
                'message' => 'No changes detected between the files.',
                'diffrence' => []
            ]);
        }

        return response()->json([
            'success' => true,
            'diffrence' => $diffrence,
        ]);
    }


    private static function highlightChanges(string $oldLine, string $newLine)
    {
        $oldWords = explode(' ', $oldLine);
        $newWords = explode(' ', $newLine);

        $diffOld = [];
        $diffNew = [];

        $maxWords = max(count($oldWords), count($newWords));
        for ($i = 0; $i < $maxWords; $i++) {
            $oldWord = $oldWords[$i] ?? '';
            $newWord = $newWords[$i] ?? '';

            if ($oldWord !== $newWord) {
                $diffOld[] = "**$oldWord**";
                $diffNew[] = "**$newWord**";
            } else {
                $diffOld[] = $oldWord;
                $diffNew[] = $newWord;
            }
        }

        return [
            'old_highlighted' => implode(' ', $diffOld),
            'new_highlighted' => implode(' ', $diffNew),
        ];
    }

        
}