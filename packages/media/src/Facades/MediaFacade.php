<?php

declare(strict_types=1);

namespace Quicktane\Media\Facades;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Quicktane\Media\Contracts\ImageFacade as ImageFacadeContract;
use Quicktane\Media\Contracts\MediaFacade as MediaFacadeContract;
use Quicktane\Media\Models\MediaFile;
use Quicktane\Media\Repositories\MediaFileRepository;
use Quicktane\Media\Repositories\MediaVariantRepository;
use Quicktane\Media\Services\FileUploadService;

class MediaFacade implements MediaFacadeContract
{
    public function __construct(
        private readonly MediaFileRepository $mediaFileRepository,
        private readonly MediaVariantRepository $mediaVariantRepository,
        private readonly FileUploadService $fileUploadService,
        private readonly ImageFacadeContract $imageFacade,
    ) {}

    public function upload(UploadedFile $file): MediaFile
    {
        $fileData = $this->fileUploadService->upload($file);
        $mediaFile = $this->mediaFileRepository->create($fileData);

        $this->imageFacade->generateVariants($mediaFile);

        return $mediaFile->load('variants');
    }

    public function getFile(string $uuid): ?MediaFile
    {
        return $this->mediaFileRepository->findByUuid($uuid);
    }

    public function deleteFile(MediaFile $mediaFile): bool
    {
        $variants = $this->mediaVariantRepository->getByMediaFile($mediaFile->id);

        foreach ($variants as $variant) {
            Storage::disk($variant->disk)->delete($variant->path);
        }

        Storage::disk($mediaFile->disk)->delete($mediaFile->path);

        $this->mediaVariantRepository->deleteByMediaFile($mediaFile->id);

        return $this->mediaFileRepository->delete($mediaFile);
    }

    public function updateFileMeta(MediaFile $mediaFile, array $data): MediaFile
    {
        return $this->mediaFileRepository->update($mediaFile, $data);
    }

    public function getFileUrl(MediaFile $mediaFile): string
    {
        return $mediaFile->url;
    }

    public function getVariantUrl(MediaFile $mediaFile, string $variantName): ?string
    {
        return $this->imageFacade->getVariantUrl($mediaFile, $variantName);
    }
}
