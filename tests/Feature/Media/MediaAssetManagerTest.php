<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Media\Application\Services\MediaAssetManager;
use App\Media\Domain\Enums\MediaType;
use App\Media\Infrastructure\Models\EloquentMedia;
use App\Media\Infrastructure\Models\EloquentMediaAsset;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MediaAssetManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_a_unified_media_asset_from_an_image(): void
    {
        Storage::fake('public');

        /** @var MediaAssetManager $manager */
        $manager = app(MediaAssetManager::class);

        $asset = $manager->createFromFile(
            UploadedFile::fake()->image('avatar.png'),
            'Profile Avatar',
        );

        $media = $asset->originalMedia();

        $this->assertInstanceOf(EloquentMediaAsset::class, $asset);
        $this->assertInstanceOf(EloquentMedia::class, $media);
        $this->assertSame(MediaType::IMAGE, $asset->media_type);
        $this->assertSame('avatar.png', $asset->original_file_name);
        $this->assertSame('png', $asset->extension);
        $this->assertSame(MediaType::IMAGE->value, $media->media_type);
        $this->assertTrue($media->is_previewable);

        $this->assertDatabaseHas('media_assets', [
            'id' => $asset->id,
            'name' => 'Profile Avatar',
            'media_type' => MediaType::IMAGE->value,
            'collection_name' => EloquentMediaAsset::ORIGINAL_COLLECTION,
        ]);

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $disk->assertExists($media->getPathRelativeToRoot());
    }

    public function test_it_detects_pdf_assets_inside_the_same_media_storage(): void
    {
        Storage::fake('public');

        /** @var MediaAssetManager $manager */
        $manager = app(MediaAssetManager::class);

        $asset = $manager->createFromFile(
            UploadedFile::fake()->create('report.pdf', 128, 'application/pdf'),
            'Quarterly Report',
        );

        $media = $asset->originalMedia();

        $this->assertInstanceOf(EloquentMedia::class, $media);
        $this->assertSame(MediaType::PDF, $asset->media_type);
        $this->assertSame(MediaType::PDF->value, $media->media_type);
        $this->assertTrue($media->is_previewable);
        $this->assertSame('pdf', $asset->extension);
        $this->assertSame('report.pdf', $asset->original_file_name);
    }

    public function test_it_rejects_file_when_selected_type_does_not_match_detected_type(): void
    {
        Storage::fake('public');

        /** @var MediaAssetManager $manager */
        $manager = app(MediaAssetManager::class);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Загруженный файл определён как');

        $manager->createFromFile(
            UploadedFile::fake()->create('report.pdf', 128, 'application/pdf'),
            'Quarterly Report',
            expectedType: MediaType::IMAGE,
        );
    }
}
