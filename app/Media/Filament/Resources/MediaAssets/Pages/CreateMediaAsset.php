<?php

declare(strict_types=1);

namespace App\Media\Filament\Resources\MediaAssets\Pages;

use App\Media\Application\Services\MediaAssetManager;
use App\Media\Domain\Enums\MediaType;
use App\Media\Filament\Resources\MediaAssets\MediaAssetResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use RuntimeException;
use Throwable;

class CreateMediaAsset extends CreateRecord
{
    protected static string $resource = MediaAssetResource::class;

    /**
     * @throws Throwable
     */
    protected function handleRecordCreation(array $data): Model
    {
        /** @var MediaAssetManager $manager */
        $manager = app(MediaAssetManager::class);
        $file = Arr::first(Arr::wrap($data['file'] ?? null));

        if (! is_string($file) && ! $file instanceof UploadedFile) {
            throw new RuntimeException('A file must be uploaded before creating a media asset.');
        }

        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;
        $userId = Auth::id();
        $expectedType = MediaType::fromFilamentState($data['media_type'] ?? null);

        return $manager->createFromFile(
            $file,
            is_string($name) ? $name : '',
            is_string($userId) ? $userId : null,
            is_string($description) && $description !== '' ? $description : null,
            expectedType: $expectedType,
        );
    }

    protected function getRedirectUrl(): string
    {
        return MediaAssetResource::getUrl('index');
    }

    public function getTitle(): string
    {
        return 'Создать медиафайл';
    }
}
