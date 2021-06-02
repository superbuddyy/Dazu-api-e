<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\File\File;

class ImageService
{
    /**
     * @var ImageManager
     */
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(['driver' => config('image.driver')]);
    }

    public function store(File $file, string $model): string
    {
        $image = $this->manager->make($file->getPathname());

        $filename = $this->makeFilename($file->getExtension());
        $pathModel = $model !== null && defined($model . '::IMAGES_PATH') ?
            constant($model . '::IMAGES_PATH') . '/' :
            'other/';

        Storage::put(
            'public/' . $pathModel . "$filename",
            (string)$image->stream($file->getExtension(), 50),
        );

        return $filename;
    }

    public function delete($filePath)
    {
        return Storage::delete('public/' . $filePath);
    }

    /**
     * Make a file name with the given extension and optional prefix.
     * @param string $extension
     * @param string $prefix
     * @return string
     */
    public function makeFilename(string $extension, string $prefix = ''): string
    {
        return $prefix . Str::random() . '.' . $extension;
    }
}
