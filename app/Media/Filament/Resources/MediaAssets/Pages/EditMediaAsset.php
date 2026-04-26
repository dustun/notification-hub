<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets\Pages;

use App\Media\Application\Services\MediaAssetManager;
use App\Media\Domain\Enums\MediaType;
use App\Media\Filament\Resources\MediaAssets\MediaAssetResource;
use App\Media\Infrastructure\Models\EloquentMediaAsset;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use RuntimeException;
use Throwable;

class EditMediaAsset extends EditRecord
{
    protected static string $resource = MediaAssetResource::class;

    /**
     * @throws Throwable
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var MediaAssetManager $manager */
        $manager = app(MediaAssetManager::class);
        $file = Arr::first(Arr::wrap($data['file'] ?? null));
        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;
        $expectedType = MediaType::fromFilamentState($data['media_type'] ?? null);

        if (! $record instanceof EloquentMediaAsset) {
            throw new RuntimeException('The edited record must be a media asset.');
        }

        return $manager->updateAsset(
            $record,
            is_string($name) ? $name : '',
            is_string($description) && $description !== '' ? $description : null,
            is_string($file) || $file instanceof UploadedFile ? $file : null,
            $expectedType,
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->label('Просмотр'),
            DeleteAction::make()->label('Удалить'),
        ];
    }

    public function getTitle(): string
    {
        return 'Редактировать медиафайл';
    }
}
