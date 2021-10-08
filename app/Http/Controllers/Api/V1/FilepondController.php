<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Sopamo\LaravelFilepond\Filepond;
use Sopamo\LaravelFilepond\Http\Controllers\FilepondController as BaseFilepondController;
use Symfony\Component\HttpFoundation\Response;

/**
 * FilepondController with delete() method fixed
 */
class FilepondController extends BaseFilepondController
{
    private Filepond $filepond;

    public function __construct(Filepond $filepond)
    {
        parent::__construct($filepond);

        $this->filepond = $filepond;
    }

    public function delete(Request $request)
    {
        $tmpFilesPath = config('filepond.temporary_files_path', 'storage/app');
        $tmpFilesDisk = config('filepond.temporary_files_disk', 'local');
        $absoluteFilePath = $this->filepond->getPathFromServerId($request->getContent());
        $storageFilePath = strstr($absoluteFilePath, $tmpFilesPath);
        $responseContent = '';
        $responseCode = Response::HTTP_NO_CONTENT;

        if (!Storage::disk($tmpFilesDisk)->deleteDirectory(dirname($storageFilePath))) {
            $responseContent = 'An error occurred while deleting the temporary directory.';
            $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        return response($responseContent, $responseCode)->header('Content-Type', 'text/plain');
    }
}
