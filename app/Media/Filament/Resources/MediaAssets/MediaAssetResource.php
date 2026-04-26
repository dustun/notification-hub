<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets;

use App\Media\Filament\Resources\MediaAssets\Pages\CreateMediaAsset;
use App\Media\Filament\Resources\MediaAssets\Pages\EditMediaAsset;
use App\Media\Filament\Resources\MediaAssets\Pages\ListMediaAssets;
use App\Media\Filament\Resources\MediaAssets\Pages\ViewMediaAsset;
use App\Media\Filament\Resources\MediaAssets\Schemas\MediaAssetForm;
use App\Media\Filament\Resources\MediaAssets\Schemas\MediaAssetInfolist;
use App\Media\Filament\Resources\MediaAssets\Tables\MediaAssetsTable;
use App\Media\Infrastructure\Models\EloquentMediaAsset;
use BackedEnum;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MediaAssetResource extends Resource
{
    protected static ?string $model = EloquentMediaAsset::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Photo;

    protected static ?int $navigationSort = 20;

    public static function getModelLabel(): string
    {
        return 'медиафайл';
    }

    public static function getPluralModelLabel(): string
    {
        return 'медиафайлы';
    }

    public static function getNavigationGroup(): string
    {
        return 'Контент';
    }

    public static function getNavigationLabel(): string
    {
        return 'Медиафайлы';
    }

    public static function form(Schema $schema): Schema
    {
        return MediaAssetForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MediaAssetInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MediaAssetsTable::configure($table);
    }

    public static function getNavigationBadge(): string
    {
        return (string) EloquentMediaAsset::query()->count();
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'primary';
    }

    /**
     * @return array<string, PageRegistration>
     */
    public static function getPages(): array
    {
        return [
            'index' => ListMediaAssets::route('/'),
            'create' => CreateMediaAsset::route('/create'),
            'view' => ViewMediaAsset::route('/{record}'),
            'edit' => EditMediaAsset::route('/{record}/edit'),
        ];
    }
}
