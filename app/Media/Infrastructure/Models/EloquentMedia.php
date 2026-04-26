<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Models;

use App\Media\Application\Support\MediaTypeDetector;
use App\Media\Domain\Enums\MediaType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property-read int $media_type
 * @property-read bool $is_previewable
 */
class EloquentMedia extends Media
{
    /** @var list<string> */
    protected $appends = [
        'original_url',
        'preview_url',
        'media_type',
        'is_previewable',
    ];

    public function detectedMediaType(): MediaType
    {
        /** @var MediaTypeDetector $detector */
        $detector = app(MediaTypeDetector::class);

        $extension = pathinfo($this->file_name, PATHINFO_EXTENSION) ?: null;

        return $detector->detect($this->mime_type, $extension);
    }

    protected function mediaType(): Attribute
    {
        return Attribute::get(
            fn(): int => $this->detectedMediaType()->value
        );
    }

    protected function isPreviewable(): Attribute
    {
        return Attribute::get(
            fn(): bool => in_array(
                $this->detectedMediaType(),
                [MediaType::IMAGE, MediaType::VIDEO, MediaType::PDF],
                true
            )
        );
    }
}
