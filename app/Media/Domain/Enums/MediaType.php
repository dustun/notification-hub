<?php

declare(strict_types=1);

namespace App\Media\Domain\Enums;

enum MediaType: int
{
    case IMAGE = 1;
    case VIDEO = 2;
    case AUDIO = 3;
    case PDF = 4;
    case DOCUMENT = 5;
    case SPREADSHEET = 6;
    case PRESENTATION = 7;
    case ARCHIVE = 8;
    case OTHER = 9;

    public function label(): string
    {
        return match ($this) {
            self::IMAGE => 'Изображение',
            self::VIDEO => 'Видео',
            self::AUDIO => 'Аудио',
            self::PDF => 'PDF',
            self::DOCUMENT => 'Документ',
            self::SPREADSHEET => 'Таблица',
            self::PRESENTATION => 'Презентация',
            self::ARCHIVE => 'Архив',
            self::OTHER => 'Файл',
        };
    }

    public function directory(): string
    {
        return match ($this) {
            self::IMAGE => 'images',
            self::VIDEO => 'videos',
            self::AUDIO => 'audio',
            self::PDF => 'pdfs',
            self::DOCUMENT => 'documents',
            self::SPREADSHEET => 'spreadsheets',
            self::PRESENTATION => 'presentations',
            self::ARCHIVE => 'archives',
            self::OTHER => 'files',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::IMAGE => 'success',
            self::VIDEO => 'warning',
            self::AUDIO => 'info',
            self::PDF => 'danger',
            self::DOCUMENT => 'primary',
            self::SPREADSHEET => 'success',
            self::PRESENTATION => 'warning',
            self::ARCHIVE => 'gray',
            self::OTHER => 'gray',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }

    /**
     * @return list<string>
     */
    public function acceptedMimeTypes(): array
    {
        return match ($this) {
            self::IMAGE => ['image/*'],
            self::VIDEO => ['video/*'],
            self::AUDIO => ['audio/*'],
            self::PDF => ['application/pdf'],
            self::DOCUMENT => [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.oasis.opendocument.text',
                'text/plain',
                'text/markdown',
                'text/csv',
                'application/rtf',
            ],
            self::SPREADSHEET => [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.oasis.opendocument.spreadsheet',
                'text/csv',
            ],
            self::PRESENTATION => [
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.oasis.opendocument.presentation',
            ],
            self::ARCHIVE => [
                'application/zip',
                'application/x-zip-compressed',
                'application/x-rar-compressed',
                'application/x-7z-compressed',
                'application/gzip',
                'application/x-tar',
            ],
            self::OTHER => ['*/*'],
        };
    }

    public static function fromFilamentState(mixed $state): self
    {
        if (! is_int($state) && ! (is_string($state) && is_numeric($state))) {
            return self::OTHER;
        }

        return self::tryFrom((int) $state) ?? self::OTHER;
    }
}
