<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets\Schemas;

use App\Media\Application\Support\MediaTypeDetector;
use App\Media\Domain\Enums\MediaType;
use App\Media\Infrastructure\Models\EloquentMediaAsset;
use App\Shared\Filament\Forms\Components\FileUploadField;
use App\Shared\Filament\Forms\Components\RichEditorField;
use App\Shared\Filament\Forms\Components\TextInputField;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Загрузка')
                    ->description('Выберите ожидаемый тип медиа и загрузите исходный файл. Итоговый тип всегда определяется по самому файлу.')
                    ->columns(2)
                    ->schema([
                        Select::make('media_type')
                            ->label('Ожидаемый тип')
                            ->options(MediaType::options())
                            ->default(MediaType::OTHER->value)
                            ->live()
                            ->native(false)
                            ->required(),

                        FileUploadField::make('file')
                            ->label('Файл')
                            ->helperText('Проверка выбранного типа выполняется после загрузки файла, чтобы браузер не блокировал корректные PDF, изображения и другие документы.')
                            ->afterStateUpdated(function (Get $get, Set $set, mixed $state): void {
                                $file = self::resolveUploadedFile($state);

                                if ($file === null) {
                                    return;
                                }

                                $originalFileName = $file->getClientOriginalName();
                                $extension = $file->getClientOriginalExtension();
                                $mimeType = $file->getMimeType();
                                $detectedType = app(MediaTypeDetector::class)->detect($mimeType, $extension);
                                $currentOriginalFileName = $get('original_file_name');
                                $currentName = $get('name');
                                $derivedName = self::deriveNameFromFile($originalFileName);
                                $previousDerivedName = is_string($currentOriginalFileName)
                                    ? self::deriveNameFromFile($currentOriginalFileName)
                                    : null;

                                $set('original_file_name', $originalFileName);
                                $set('extension', Str::lower($extension));
                                $set('mime_type', $mimeType);
                                $set('media_type', $detectedType->value);

                                if (
                                    ! is_string($currentName)
                                    || trim($currentName) === ''
                                    || ($previousDerivedName !== null && trim($currentName) === $previousDerivedName)
                                ) {
                                    $set('name', $derivedName);
                                }
                            })
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->columnSpanFull(),
                    ]),

                Section::make('Информация о медиафайле')
                    ->columns(2)
                    ->schema([
                        TextInputField::make('name')
                            ->label('Название')
                            ->prefixIcon(Heroicon::OutlinedTag)
                            ->required(),

                        TextInputField::make('collection_name')
                            ->label('Коллекция')
                            ->default('original')
                            ->disabled()
                            ->dehydrated(false),

                        TextInputField::make('original_file_name')
                            ->label('Имя исходного файла')
                            ->disabled()
                            ->dehydrated(false),

                        TextInputField::make('mime_type')
                            ->label('MIME-тип')
                            ->disabled()
                            ->dehydrated(false),

                        TextInputField::make('extension')
                            ->label('Расширение')
                            ->disabled()
                            ->dehydrated(false),

                        TextInputField::make('disk')
                            ->label('Диск')
                            ->default(function (): string {
                                $storageDisk = config('filesystems.storage', 'public');

                                return is_string($storageDisk) ? $storageDisk : 'public';
                            })
                            ->disabled()
                            ->dehydrated(false),

                        Placeholder::make('detected_size_readable')
                            ->label('Размер')
                            ->content(fn(Get $get, $record): string => self::resolveReadableSize($get('file'), $record)),

                        Placeholder::make('uploaded_by_name')
                            ->label('Загрузил')
                            ->content(fn($record): string => $record instanceof EloquentMediaAsset ? ($record->uploadedBy->email ?? 'system') : 'system'),

                        RichEditorField::make('description')
                            ->label('Описание')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    private static function resolveUploadedFile(mixed $state): ?UploadedFile
    {
        $file = Arr::first(Arr::wrap($state));

        return $file instanceof UploadedFile
            ? $file
            : null;
    }

    private static function deriveNameFromFile(string $originalFileName): string
    {
        $fileName = pathinfo($originalFileName, PATHINFO_FILENAME);
        $fileName = trim($fileName);

        return $fileName !== ''
            ? Str::of($fileName)->replace(['-', '_'], ' ')->squish()->title()->toString()
            : 'Новый медиафайл';
    }

    private static function resolveReadableSize(mixed $fileState, mixed $record): string
    {
        $file = self::resolveUploadedFile($fileState);

        if ($file !== null) {
            return Number::fileSize((int) $file->getSize());
        }

        return $record instanceof EloquentMediaAsset
            ? $record->human_readable_size
            : '0 B';
    }
}
