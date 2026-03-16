<?php

declare(strict_types=1);

namespace Quicktane\Media\Contracts;

use Quicktane\Media\Models\MediaFile;
use Quicktane\Media\Models\MediaVariant;

interface ImageFacade
{
    /**
     * @return array<MediaVariant>
     */
    public function generateVariants(MediaFile $mediaFile): array;

    public function generateVariant(MediaFile $mediaFile, string $variantName): ?MediaVariant;

    public function getVariantUrl(MediaFile $mediaFile, string $variantName): ?string;
}
