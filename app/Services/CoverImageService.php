<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class CoverImageService
{
    public function store(UploadedFile $file): string
    {
        $name = 'covers/'.Str::uuid().'.webp';
        $manager = new ImageManager(new Driver);
        $encoded = $manager->read($file->getRealPath())
            ->scaleDown(width: 1600)
            ->toWebp(82);

        Storage::disk('public')->put($name, (string) $encoded);

        return $name;
    }

    public function replace(?string $current, UploadedFile $file): string
    {
        $path = $this->store($file);

        if ($current) {
            Storage::disk('public')->delete($current);
        }

        return $path;
    }
}
