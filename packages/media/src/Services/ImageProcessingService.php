<?php

declare(strict_types=1);

namespace Quicktane\Media\Services;

use Illuminate\Support\Facades\Storage;
use Quicktane\Media\Models\MediaFile;

class ImageProcessingService
{
    private const array SUPPORTED_MIME_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    public function generateVariants(MediaFile $mediaFile): array
    {
        if (! $this->isProcessableImage($mediaFile->mime_type)) {
            return [];
        }

        $variantDefinitions = config('media.media.image_variants', []);
        $variants = [];

        foreach ($variantDefinitions as $variantName => $dimensions) {
            $variantData = $this->generateVariant($mediaFile, $variantName, $dimensions[0], $dimensions[1]);

            if ($variantData !== null) {
                $variants[] = $variantData;
            }
        }

        return $variants;
    }

    public function generateVariant(MediaFile $mediaFile, string $variantName, int $maxWidth, int $maxHeight): ?array
    {
        if (! $this->isProcessableImage($mediaFile->mime_type)) {
            return null;
        }

        $disk = Storage::disk($mediaFile->disk);
        $sourcePath = $disk->path($mediaFile->path);

        $sourceImage = $this->createImageFromFile($sourcePath, $mediaFile->mime_type);

        if ($sourceImage === null) {
            return null;
        }

        $originalWidth = imagesx($sourceImage);
        $originalHeight = imagesy($sourceImage);

        $resizedDimensions = $this->calculateResizedDimensions(
            $originalWidth,
            $originalHeight,
            $maxWidth,
            $maxHeight,
        );

        $resizedImage = imagecreatetruecolor($resizedDimensions['width'], $resizedDimensions['height']);

        if ($resizedImage === false) {
            imagedestroy($sourceImage);

            return null;
        }

        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $resizedDimensions['width'],
            $resizedDimensions['height'],
            $originalWidth,
            $originalHeight,
        );

        $variantPath = $this->buildVariantPath($mediaFile->path, $variantName);
        $variantFullPath = $disk->path($variantPath);

        $webpQuality = (int) config('media.media.webp_quality', 85);
        imagewebp($resizedImage, $variantFullPath, $webpQuality);

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return [
            'variant_name' => $variantName,
            'disk' => $mediaFile->disk,
            'path' => $variantPath,
            'mime_type' => 'image/webp',
            'size' => filesize($variantFullPath),
            'width' => $resizedDimensions['width'],
            'height' => $resizedDimensions['height'],
        ];
    }

    private function isProcessableImage(string $mimeType): bool
    {
        return in_array($mimeType, self::SUPPORTED_MIME_TYPES, true);
    }

    private function createImageFromFile(string $filePath, string $mimeType): ?\GdImage
    {
        $image = match ($mimeType) {
            'image/jpeg' => @imagecreatefromjpeg($filePath),
            'image/png' => @imagecreatefrompng($filePath),
            'image/gif' => @imagecreatefromgif($filePath),
            'image/webp' => @imagecreatefromwebp($filePath),
            default => false,
        };

        return $image !== false ? $image : null;
    }

    private function calculateResizedDimensions(
        int $originalWidth,
        int $originalHeight,
        int $maxWidth,
        int $maxHeight,
    ): array {
        $ratioWidth = $maxWidth / $originalWidth;
        $ratioHeight = $maxHeight / $originalHeight;
        $ratio = min($ratioWidth, $ratioHeight, 1.0);

        return [
            'width' => (int) round($originalWidth * $ratio),
            'height' => (int) round($originalHeight * $ratio),
        ];
    }

    private function buildVariantPath(string $originalPath, string $variantName): string
    {
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'] ?? '';
        $filenameWithoutExtension = $pathInfo['filename'] ?? '';

        return $directory.'/'.$filenameWithoutExtension.'_'.$variantName.'.webp';
    }
}
