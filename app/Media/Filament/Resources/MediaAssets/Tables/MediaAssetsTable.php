<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets\Tables;

use App\Media\Infrastructure\Models\EloquentMediaAsset;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MediaAssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('preview_url')
                    ->label('Превью')
                    ->state(fn(EloquentMediaAsset $record): ?string => $record->isImage() ? $record->preview_url : null)
                    ->circular(),

                TextColumn::make('name')
                    ->label('Название')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn(EloquentMediaAsset $record): string => $record->original_file_name ?? '-'),

                TextColumn::make('media_type')
                    ->label('Тип')
                    ->badge()
                    ->formatStateUsing(fn(EloquentMediaAsset $record): string => $record->media_type->label())
                    ->color(fn(EloquentMediaAsset $record): string => $record->media_type->badgeColor())
                    ->sortable(),

                TextColumn::make('human_readable_size')
                    ->label('Размер')
                    ->sortable(query: function (Builder $query, string $direction) {
                        return $query->orderBy('size', $direction);
                    }),

                TextColumn::make('mime_type')
                    ->label('MIME-тип')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('disk')
                    ->label('Диск')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Создано')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('media_type')
                    ->label('Тип медиа')
                    ->options(\App\Media\Domain\Enums\MediaType::options()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()->label('Открыть'),
                    EditAction::make()->label('Редактировать'),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->label('Удалить выбранное'),
                ]),
            ])
            ->emptyStateHeading('Медиафайлы ещё не загружены')
            ->emptyStateDescription('Начните с загрузки первого файла в общее медиа-хранилище.');
    }
}
