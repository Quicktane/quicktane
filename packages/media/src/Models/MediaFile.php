<?php

declare(strict_types=1);

namespace Quicktane\Media\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaFile extends Model
{
    protected $fillable = [
        'uuid',
        'disk',
        'path',
        'filename',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'title',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class);
    }

    protected function url(): Attribute
    {
        return Attribute::get(fn (): string => Storage::disk($this->disk)->url($this->path));
    }

    protected static function booted(): void
    {
        static::creating(function (MediaFile $mediaFile): void {
            if (empty($mediaFile->uuid)) {
                $mediaFile->uuid = (string) Str::uuid();
            }
        });
    }
}
