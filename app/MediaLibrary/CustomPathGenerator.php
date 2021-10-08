<?php


namespace App\MediaLibrary;


use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\DefaultPathGenerator;

class CustomPathGenerator extends DefaultPathGenerator
{
    protected function getBasePath(Media $media): string
    {
        $prefix = config('media-library.prefix', '');
        $basePath = md5($media->getKey() . config('app.key'));

        return $prefix !== ''
            ? "$prefix/$basePath"
            : $basePath;
    }
}
