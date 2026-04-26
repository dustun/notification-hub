<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Support;

use App\Media\Application\Support\MediaTypeDetector;
use App\Media\Domain\Enums\MediaType;
use App\Media\Infrastructure\Models\EloquentMedia;
use Carbon\CarbonInterface;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class MediaPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->basePath($media) . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->basePath($media) . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->basePath($media) . '/responsive-images/';
    }

    private function basePath(Media $media): string
    {
        $timestamp = $media->created_at instanceof CarbonInterface
            ? $media->created_at
            : now();
        $ownerKey = $media->getAttributeValue('model_id');
        $ownerKey = is_string($ownerKey) || is_int($ownerKey)
            ? (string) $ownerKey
            : 'unassigned';
        $mediaKey = $media->getKey();
        $mediaKey = is_string($mediaKey) || is_int($mediaKey)
            ? (string) $mediaKey
            : 'media';

        return sprintf(
            '%s/%s/%s/%s/%s',
            $this->resolveMediaType($media)->directory(),
            $timestamp->format('Y'),
            $timestamp->format('m'),
            $ownerKey,
            $mediaKey,
        );
    }

    private function resolveMediaType(Media $media): MediaType
    {
        if ($media instanceof EloquentMedia) {
            return $media->detectedMediaType();
        }

        /** @var MediaTypeDetector $detector */
        $detector = app(MediaTypeDetector::class);

        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION) ?: null;

        return $detector->detect($media->mime_type, $extension);
    }
}
