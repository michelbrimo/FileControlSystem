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
            'group_id' => 'required',
        ]);
    
        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }
        
        $results = []; 
        foreach ($data['file_path'] as $filePath) {
            if (!file_exists($filePath)) throw new Exception($filePath." does not exist.", 404);

            $result = $this->processSingleFile([
                'file_path' => $filePath,
                'group_id' => $data['group_id'],
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

        if($this->file_repository->getFile_byName($fileName))
            throw new Exception($uniqueFileName." already exists in your group.", 400);
        
        $storagePath = 'files/' . $uniqueFileName . '/' . $nameWithoutExtension . "_original." .  $fileExtension;
        
        $fileContents = file_get_contents($originalFilePath);
        Storage::disk('public')->put($storagePath, $fileContents);

        $file =  $this->file_repository->createFile([
            'state' => 0,
            'file_name' => $fileName,
            'file_path' => $storagePath,
            'group_id' => $data['group_id'],
            'owner_id' => auth()->user()->id
        ]); 
        
        $this->file_repository->createHistory([
            'link' => $file->file_path,
            'user_id' => auth()->user()->id,
            'file_id' => $file->id
        ]); 

        return $file;
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
            $results[] = $this->file_repository->createCheck([
                'user_id' => auth()->user()->id,
                'file_id' => $file_id
            ]);

            $this->file_repository->updateFile( $file_id, ["state" => 1]);
        }

        return $results; 
    }

    function checkOut($data) {
        $validator = Validator::make($data, [
            'file_path' => 'required|string', 
            'file_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            throw new Exception(
                $validator->errors()->first(),
                422
            );
        }

        $file = $this->file_repository->getFile_byId($data['file_id']);

        $originalFilePath = $data['file_path'];

        $fileName = basename($originalFilePath); 
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


        $storagePath = 'files/' . $uniqueFileName . '/' . $nameWithoutExtension . "_v2." . $fileExtension;
        $fileContents = file_get_contents($originalFilePath);
        Storage::disk('public')->put($storagePath, $fileContents);

        $this->file_repository->updateFile($data['file_id'], [
            'state' => 0,
            'file_path' => $storagePath
        ]);

        $this->file_repository->deleteCheck([
            'check_id' => $check_in->id,
        ]);

        return $this->file_repository->createHistory([
            'link' => $storagePath,
            'user_id' => auth()->user()->id,
            'file_id' => $data['file_id']
        ]); 
    }









    
    public static function compareFiles(string $oldFilePath, string $newFilePath)
    {
        
        if (!file_exists($oldFilePath) || !file_exists($newFilePath)) {
            throw new \Exception("One or both files do not exist.");
        }

        $oldLines = file($oldFilePath, FILE_IGNORE_NEW_LINES);
        $newLines = file($newFilePath, FILE_IGNORE_NEW_LINES);

        $diff = [];
        $maxLines = max(count($oldLines), count($newLines));

        for ($i = 0; $i < $maxLines; $i++) {
            $oldLine = $oldLines[$i] ?? null;
            $newLine = $newLines[$i] ?? null;

            if (is_null($oldLine)) {
                $diff[] = [
                    'type' => 'added',
                    'line' => $i + 1,
                    'content' => $newLine,
                ];
            }
            elseif (is_null($newLine)) {
                $diff[] = [
                    'type' => 'removed',
                    'line' => $i + 1,
                    'content' => $oldLine,
                ];
            }
            elseif ($oldLine !== $newLine) {
                $diff[] = [
                    'type' => 'modified',
                    'line' => $i + 1,
                    'old_content' => $oldLine,
                    'new_content' => $newLine,
                    'changes' => self::highlightChanges($oldLine, $newLine),
                ];
            }
        }

        if (empty($diff)) {
            return [
                'message' => 'No changes detected between the files.',
                'diff' => []
            ];
        }

        return $diff;

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