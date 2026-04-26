<?php

declare(strict_types=1);

namespace App\Media\Application\Support;

use App\Media\Domain\Enums\MediaType;

class MediaTypeDetector
{
    public function detect(?string $mimeType, ?string $extension = null): MediaType
    {
        $normalizedMimeType = is_string($mimeType)
            ? strtolower(trim($mimeType))
            : null;

        $normalizedExtension = is_string($extension)
            ? strtolower(trim($extension, '. '))
            : null;

        return $this->detectFromMimeType($normalizedMimeType)
            ?? $this->detectFromExtension($normalizedExtension)
            ?? MediaType::OTHER;
    }

    private function detectFromMimeType(?string $mimeType): ?MediaType
    {
        if ($mimeType === null || $mimeType === '') {
            return null;
        }

        if (str_starts_with($mimeType, 'image/')) {
            return MediaType::IMAGE;
        }

        if (str_starts_with($mimeType, 'video/')) {
            return MediaType::VIDEO;
        }

        if (str_starts_with($mimeType, 'audio/')) {
            return MediaType::AUDIO;
        }

        return match ($mimeType) {
            'application/pdf' => MediaType::PDF,
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.text',
            'text/plain',
            'text/markdown',
            'text/csv',
            'application/rtf' => MediaType::DOCUMENT,
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.oasis.opendocument.spreadsheet' => MediaType::SPREADSHEET,
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.oasis.opendocument.presentation' => MediaType::PRESENTATION,
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
            'application/gzip',
            'application/x-tar' => MediaType::ARCHIVE,
            default => null,
        };
    }

    private function detectFromExtension(?string $extension): ?MediaType
    {
        if ($extension === null || $extension === '') {
            return null;
        }

        return match ($extension) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif', 'bmp', 'tiff' => MediaType::IMAGE,
            'mp4', 'mov', 'avi', 'mkv', 'webm', 'm4v' => MediaType::VIDEO,
            'mp3', 'wav', 'ogg', 'aac', 'flac', 'm4a' => MediaType::AUDIO,
            'pdf' => MediaType::PDF,
            'doc', 'docx', 'odt', 'txt', 'md', 'csv', 'rtf' => MediaType::DOCUMENT,
            'xls', 'xlsx', 'ods' => MediaType::SPREADSHEET,
            'ppt', 'pptx', 'odp' => MediaType::PRESENTATION,
            'zip', 'rar', '7z', 'gz', 'tar' => MediaType::ARCHIVE,
            default => null,
        };
    }
}
