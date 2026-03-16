<?php

declare(strict_types=1);

namespace Quicktane\Media\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    public function upload(UploadedFile $file, ?string $disk = null): array
    {
        $disk = $disk ?? config('media.media.disk', 'public');
        $uniqueFilename = Str::uuid()->toString().'.'.$file->getClientOriginalExtension();
        $directory = 'media/'.date('Y/m');
        $path = $file->storeAs($directory, $uniqueFilename, $disk);

        $metadata = [
            'disk' => $disk,
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'width' => null,
            'height' => null,
        ];

        if ($this->isImage($file->getMimeType())) {
            $dimensions = $this->getImageDimensions(Storage::disk($disk)->path($path));

            if ($dimensions !== null) {
                $metadata['width'] = $dimensions[0];
                $metadata['height'] = $dimensions[1];
            }
        }

        return $metadata;
    }

    private function isImage(?string $mimeType): bool
    {
        if ($mimeType === null) {
            return false;
        }

        return in_array($mimeType, [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
        ], true);
    }

    private function getImageDimensions(string $filePath): ?array
    {
        $imageSize = @getimagesize($filePath);

        if ($imageSize === false) {
            return null;
        }

        return [$imageSize[0], $imageSize[1]];
    }
}
