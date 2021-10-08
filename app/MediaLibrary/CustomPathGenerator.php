<?php


namespace App\MediaLibrary;


use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class CustomPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getBasePath($media) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getBasePath($media) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getBasePath($media) . '/responsive-images/';
    }

    private function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');
        $basePath = md5($media->getKey() . config('app.key'));

        return $prefix !== ''
            ? "$prefix/$basePath"
            : $basePath;
    }
}
