<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets\Pages;

use App\Media\Filament\Resources\MediaAssets\MediaAssetResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMediaAsset extends ViewRecord
{
    protected static string $resource = MediaAssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->label('Редактировать'),
        ];
    }

    public function getTitle(): string
    {
        return 'Просмотр медиафайла';
    }
}
