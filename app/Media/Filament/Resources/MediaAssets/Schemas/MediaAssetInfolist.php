<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets\Schemas;

use App\Media\Infrastructure\Models\EloquentMediaAsset;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaAssetInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Предпросмотр')
                    ->schema([
                        ImageEntry::make('preview_url')
                            ->label('Предпросмотр')
                            ->state(fn(EloquentMediaAsset $record): ?string => $record->isImage() ? $record->preview_url : null)
                            ->hidden(fn(EloquentMediaAsset $record): bool => ! $record->isImage()),

                        TextEntry::make('original_url')
                            ->label('Оригинальный файл')
                            ->state(fn(EloquentMediaAsset $record): ?string => $record->original_url)
                            ->url(fn(EloquentMediaAsset $record): ?string => $record->original_url, shouldOpenInNewTab: true)
                            ->copyable(),
                    ]),

                Section::make('Информация о медиафайле')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')->label('Название'),
                        TextEntry::make('media_type')
                            ->label('Тип')
                            ->badge()
                            ->formatStateUsing(fn(EloquentMediaAsset $record): string => $record->media_type->label())
                            ->color(fn(EloquentMediaAsset $record): string => $record->media_type->badgeColor()),
                        TextEntry::make('original_file_name')->label('Имя исходного файла')->copyable(),
                        TextEntry::make('mime_type')->label('MIME-тип')->placeholder('-'),
                        TextEntry::make('extension')->label('Расширение')->placeholder('-'),
                        TextEntry::make('disk')->label('Диск'),
                        TextEntry::make('collection_name')->label('Коллекция'),
                        TextEntry::make('human_readable_size')->label('Размер'),
                    ]),

                Section::make('Описание')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Описание')
                            ->html()
                            ->placeholder('-'),
                    ]),

                Section::make('Системная информация')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('UUID')->copyable(),
                        TextEntry::make('uploadedBy.email')->label('Загрузил')->placeholder('system'),
                        TextEntry::make('created_at')->label('Создано')->dateTime('d.m.Y H:i'),
                        TextEntry::make('updated_at')->label('Обновлено')->dateTime('d.m.Y H:i'),
                    ]),
            ]);
    }
}
