<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets\Pages;

use App\Media\Filament\Resources\MediaAssets\MediaAssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListMediaAssets extends ListRecords
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Новый медиафайл')
                ->icon(Heroicon::OutlinedPlus),
        ];
    }
}
