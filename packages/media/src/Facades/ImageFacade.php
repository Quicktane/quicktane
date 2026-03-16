<?php

declare(strict_types=1);

namespace Quicktane\Media\Facades;

use Quicktane\Media\Contracts\ImageFacade as ImageFacadeContract;
use Quicktane\Media\Models\MediaFile;
use Quicktane\Media\Models\MediaVariant;
use Quicktane\Media\Repositories\MediaVariantRepository;
use Quicktane\Media\Services\ImageProcessingService;

class ImageFacade implements ImageFacadeContract
{
    public function __construct(
        private readonly MediaVariantRepository $mediaVariantRepository,
        private readonly ImageProcessingService $imageProcessingService,
    ) {}

    /**
     * @return array<MediaVariant>
     */
    public function generateVariants(MediaFile $mediaFile): array
    {
        $variantsData = $this->imageProcessingService->generateVariants($mediaFile);
        $createdVariants = [];

        foreach ($variantsData as $variantData) {
            $variantData['media_file_id'] = $mediaFile->id;
            $createdVariants[] = $this->mediaVariantRepository->create($variantData);
        }

        return $createdVariants;
    }

    public function generateVariant(MediaFile $mediaFile, string $variantName): ?MediaVariant
    {
        $variantDefinitions = config('media.media.image_variants', []);

        if (! isset($variantDefinitions[$variantName])) {
            return null;
        }

        $dimensions = $variantDefinitions[$variantName];
        $variantData = $this->imageProcessingService->generateVariant(
            $mediaFile,
            $variantName,
            $dimensions[0],
            $dimensions[1],
        );

        if ($variantData === null) {
            return null;
        }

        $variantData['media_file_id'] = $mediaFile->id;

        $existingVariant = $this->mediaVariantRepository->findByMediaFileAndVariant(
            $mediaFile->id,
            $variantName,
        );

        if ($existingVariant !== null) {
            $existingVariant->update($variantData);

            return $existingVariant;
        }

        return $this->mediaVariantRepository->create($variantData);
    }

    public function getVariantUrl(MediaFile $mediaFile, string $variantName): ?string
    {
        $variant = $this->mediaVariantRepository->findByMediaFileAndVariant(
            $mediaFile->id,
            $variantName,
        );

        if ($variant === null) {
            return null;
        }

        return $variant->url;
    }
}
