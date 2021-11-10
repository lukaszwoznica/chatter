<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UploadAvatarRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ReflectionClass;
use Sopamo\LaravelFilepond\Filepond;
use Symfony\Component\HttpFoundation\Response;

class AvatarController extends Controller
{
    public function store(UploadAvatarRequest $request, UserService $userService, Filepond $filepond)
    {
        try {
            $tempAvatarPath = $filepond->getPathFromServerId($request->avatar_server_id);

            $uploadedAvatar = $userService->uploadUserAvatar(Auth::user(), $tempAvatarPath);

            $this->deleteTempAvatarDirectory($tempAvatarPath);

            return response()->json([
                'message' => 'Avatar has been successfully uploaded.',
                'data' => [
                    'avatar_url' => $uploadedAvatar?->getUrl(),
                    'avatar_thumb_url' => $uploadedAvatar?->getUrl('thumb')
                ]
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'Something went wrong while uploading the avatar file.',
                'error' => (new ReflectionClass($exception))->getShortName()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy()
    {
        Auth::user()->clearMediaCollection('avatar');

        return response()->noContent();
    }

    private function deleteTempAvatarDirectory(string $tempAvatarPath)
    {
        $tmpFilesPath = config('filepond.temporary_files_path');
        $tmpFilesDisk = config('filepond.temporary_files_disk');
        $tmpDirectoryPath = $tmpFilesPath . '/' . basename(dirname($tempAvatarPath));

        Storage::disk($tmpFilesDisk)->deleteDirectory($tmpDirectoryPath);
    }
}
