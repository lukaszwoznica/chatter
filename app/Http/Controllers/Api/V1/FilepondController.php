<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Sopamo\LaravelFilepond\Exceptions\InvalidPathException;
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
        try {
            $directoryToDelete = $this->getDirectoryNameToDelete($request->getContent());

            if (!Storage::disk(config('filepond.temporary_files_disk'))->deleteDirectory($directoryToDelete)) {
                $responseContent = 'An error occurred while deleting the temporary directory.';
                $responseCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            }
        } catch (InvalidPathException | DecryptException $exception) {
            $responseContent = 'Invalid Filepond server id.';
            $responseCode = Response::HTTP_UNPROCESSABLE_ENTITY;
        }

        return response($responseContent ?? '', $responseCode ?? Response::HTTP_NO_CONTENT)
            ->header('Content-Type', 'text/plain');
    }

    private function getDirectoryNameToDelete(string $serverId): string
    {
        $absoluteFilePath = $this->filepond->getPathFromServerId($serverId);
        $storageFilePath = strstr($absoluteFilePath, config('filepond.temporary_files_path'));

        return dirname($storageFilePath);
    }
}
