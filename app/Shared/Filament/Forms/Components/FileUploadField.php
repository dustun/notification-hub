<?php

declare(strict_types=1);

namespace App\Shared\Filament\Forms\Components;

use Filament\Forms\Components\FileUpload;

class FileUploadField
{
    public static function make(string $name): FileUpload
    {
        $configuredMaxFileSize = config('media-library.max_file_size', 1024 * 1024 * 10);
        $configuredMaxFileSize = is_int($configuredMaxFileSize) ? $configuredMaxFileSize : 1024 * 1024 * 10;
        $maxSize = (int) ceil($configuredMaxFileSize / 1024);

        return FileUpload::make($name)
            ->translateLabel()
            ->storeFiles(false)
            ->preserveFilenames()
            ->openable()
            ->downloadable()
            ->previewable()
            ->maxSize($maxSize > 0 ? $maxSize : 10240);
    }
}
