<?php

declare(strict_types=1);

namespace App\Media\Infrastructure\Models;

use App\Auth\Infrastructure\Models\EloquentUser;
use App\Media\Domain\Enums\MediaType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property string $id
 * @property string $name
 * @property MediaType $media_type
 * @property string $collection_name
 * @property string $disk
 * @property string|null $original_file_name
 * @property string|null $mime_type
 * @property string|null $extension
 * @property int $size
 * @property string|null $uploaded_by
 * @property string|null $description
 * @property-read EloquentUser|null $uploadedBy
 * @property-read string $human_readable_size
 * @property-read string|null $preview_url
 * @property-read string|null $original_url
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EloquentMediaAsset extends Model implements HasMedia
{
    use HasUuids;
    use InteractsWithMedia;

    public const string ORIGINAL_COLLECTION = 'original';

    protected $table = 'media_assets';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'media_type',
        'collection_name',
        'disk',
        'original_file_name',
        'mime_type',
        'extension',
        'size',
        'uploaded_by',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'media_type' => MediaType::class,
            'size' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<EloquentUser, $this>
     */
    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(EloquentUser::class, 'uploaded_by');
    }

    public function registerMediaCollections(): void
    {
        $diskName = config('media-library.disk_name', 'public');

        $this
            ->addMediaCollection(self::ORIGINAL_COLLECTION)
            ->singleFile()
            ->useDisk(is_string($diskName) ? $diskName : 'public');
    }

    public function originalMedia(): ?EloquentMedia
    {
        $media = $this->getFirstMedia(self::ORIGINAL_COLLECTION);

        return $media instanceof EloquentMedia
            ? $media
            : null;
    }

    public function syncFromMedia(EloquentMedia $media): void
    {
        $extension = pathinfo($media->file_name, PATHINFO_EXTENSION) ?: null;

        $this->forceFill([
            'name' => $media->name,
            'media_type' => $media->detectedMediaType(),
            'collection_name' => $media->collection_name,
            'disk' => $media->disk,
            'original_file_name' => $media->file_name,
            'mime_type' => $media->mime_type,
            'extension' => $extension !== null ? strtolower($extension) : null,
            'size' => $media->size,
        ])->save();
    }

    public function isImage(): bool
    {
        return $this->media_type === MediaType::IMAGE;
    }

    protected function humanReadableSize(): Attribute
    {
        return Attribute::get(
            fn(): string => Number::fileSize($this->size)
        );
    }

    protected function previewUrl(): Attribute
    {
        return Attribute::get(
            fn(): ?string => $this->originalMedia()?->preview_url
        );
    }

    protected function originalUrl(): Attribute
    {
        return Attribute::get(
            fn(): ?string => $this->originalMedia()?->original_url
        );
    }
}
