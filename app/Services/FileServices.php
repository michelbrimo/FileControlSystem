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
            'file_id' => 'integer'
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