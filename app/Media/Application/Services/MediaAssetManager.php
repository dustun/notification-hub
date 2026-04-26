<?php

declare(strict_types=1);

namespace App\Media\Application\Services;

use App\Media\Domain\Enums\MediaType;
use App\Media\Infrastructure\Models\EloquentMedia;
use App\Media\Infrastructure\Models\EloquentMediaAsset;
use App\Shared\Infrastructure\Services\SystemLogManager;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

readonly class MediaAssetManager
{
    public function __construct(
        private DatabaseManager $database,
        private \App\Media\Application\Support\MediaTypeDetector $mediaTypeDetector,
        private SystemLogManager $systemLogManager,
    ) {}

    /**
     * @param string|UploadedFile $file
     * @param array<string, mixed> $customProperties
     * @throws Throwable
     */
    public function createFromFile(
        string|UploadedFile $file,
        string $name,
        ?string $uploadedBy = null,
        ?string $description = null,
        array $customProperties = [],
        ?MediaType $expectedType = null,
    ): EloquentMediaAsset {
        /** @var EloquentMediaAsset $asset */
        $asset = $this->database->transaction(function () use (
            $file,
            $name,
            $uploadedBy,
            $description,
            $customProperties,
            $expectedType,
        ): EloquentMediaAsset {
            if ($file instanceof UploadedFile) {
                $this->assertExpectedMediaType($file, $expectedType);
            }

            $diskName = config('media-library.disk_name', 'public');

            $asset = EloquentMediaAsset::query()->create([
                'name' => $name,
                'media_type' => MediaType::OTHER,
                'collection_name' => EloquentMediaAsset::ORIGINAL_COLLECTION,
                'disk' => is_string($diskName) ? $diskName : 'public',
                'uploaded_by' => $uploadedBy,
                'description' => $description,
            ]);

            $media = $asset
                ->addMedia($file)
                ->usingName($name)
                ->withCustomProperties($customProperties)
                ->toMediaCollection(EloquentMediaAsset::ORIGINAL_COLLECTION);

            if (! $media instanceof EloquentMedia) {
                throw new RuntimeException('Unexpected media model returned from media library.');
            }

            $asset->syncFromMedia($media);

            /** @var EloquentMediaAsset $freshAsset */
            $freshAsset = EloquentMediaAsset::query()->findOrFail($asset->getKey());

            return $freshAsset;
        });

        $this->systemLogManager->info(
            category: 'media',
            message: sprintf('Создан новый медиафайл "%s".', $asset->name),
            action: 'create',
            context: [
                'media_asset_id' => $asset->id,
                'media_type' => $asset->media_type->label(),
                'mime_type' => $asset->mime_type,
                'size' => $asset->size,
            ],
            userId: $uploadedBy,
        );

        return $asset;
    }

    /**
     * @throws Throwable
     */
    public function updateAsset(
        EloquentMediaAsset $asset,
        string $name,
        ?string $description = null,
        string|UploadedFile|null $file = null,
        ?MediaType $expectedType = null,
    ): EloquentMediaAsset {
        /** @var EloquentMediaAsset $updatedAsset */
        $updatedAsset = $this->database->transaction(function () use ($asset, $name, $description, $file, $expectedType): EloquentMediaAsset {
            $asset->forceFill([
                'name' => $name,
                'description' => $description,
            ])->save();

            if ($file !== null) {
                if ($file instanceof UploadedFile) {
                    $this->assertExpectedMediaType($file, $expectedType);
                }

                $asset->clearMediaCollection(EloquentMediaAsset::ORIGINAL_COLLECTION);

                $media = $asset
                    ->addMedia($file)
                    ->usingName($name)
                    ->toMediaCollection(EloquentMediaAsset::ORIGINAL_COLLECTION);

                if (! $media instanceof EloquentMedia) {
                    throw new RuntimeException('Unexpected media model returned from media library.');
                }

                $asset->syncFromMedia($media);
            } elseif (($media = $asset->originalMedia()) instanceof EloquentMedia) {
                $media->name = $name;
                $media->save();
            }

            /** @var EloquentMediaAsset $freshAsset */
            $freshAsset = EloquentMediaAsset::query()->findOrFail($asset->getKey());

            return $freshAsset;
        });

        $uploadedBy = is_string($updatedAsset->uploaded_by) ? $updatedAsset->uploaded_by : null;

        $this->systemLogManager->info(
            category: 'media',
            message: sprintf('Обновлён медиафайл "%s".', $updatedAsset->name),
            action: 'update',
            context: [
                'media_asset_id' => $updatedAsset->id,
                'media_type' => $updatedAsset->media_type->label(),
                'mime_type' => $updatedAsset->mime_type,
                'size' => $updatedAsset->size,
                'file_replaced' => $file !== null,
            ],
            userId: $uploadedBy,
        );

        return $updatedAsset;
    }

    private function assertExpectedMediaType(UploadedFile $file, ?MediaType $expectedType): void
    {
        if ($expectedType === null || $expectedType === MediaType::OTHER) {
            return;
        }

        $actualType = $this->mediaTypeDetector->detect(
            $file->getMimeType(),
            $file->getClientOriginalExtension()
        );

        if ($actualType === $expectedType) {
            return;
        }

        throw ValidationException::withMessages([
            'file' => sprintf(
                'Загруженный файл определён как "%s", а выбран тип "%s".',
                $actualType->label(),
                $expectedType->label()
            ),
        ]);
    }
}
