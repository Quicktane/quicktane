<?php

declare(strict_types=1);

namespace Quicktane\Media\Contracts;

use Illuminate\Http\UploadedFile;
use Quicktane\Media\Models\MediaFile;

interface MediaFacade
{
    public function upload(UploadedFile $file): MediaFile;

    public function getFile(string $uuid): ?MediaFile;

    public function deleteFile(MediaFile $mediaFile): bool;

    public function updateFileMeta(MediaFile $mediaFile, array $data): MediaFile;

    public function getFileUrl(MediaFile $mediaFile): string;

    public function getVariantUrl(MediaFile $mediaFile, string $variantName): ?string;
}
