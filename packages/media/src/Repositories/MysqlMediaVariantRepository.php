<?php

declare(strict_types=1);

namespace Quicktane\Media\Repositories;

use Illuminate\Support\Collection;
use Quicktane\Media\Models\MediaVariant;

class MysqlMediaVariantRepository implements MediaVariantRepository
{
    public function __construct(
        private readonly MediaVariant $mediaVariantModel,
    ) {}

    public function findById(int $id): ?MediaVariant
    {
        return $this->mediaVariantModel->newQuery()->find($id);
    }

    public function getByMediaFile(int $mediaFileId): Collection
    {
        return $this->mediaVariantModel->newQuery()
            ->where('media_file_id', $mediaFileId)
            ->get();
    }

    public function findByMediaFileAndVariant(int $mediaFileId, string $variantName): ?MediaVariant
    {
        return $this->mediaVariantModel->newQuery()
            ->where('media_file_id', $mediaFileId)
            ->where('variant_name', $variantName)
            ->first();
    }

    public function create(array $data): MediaVariant
    {
        return $this->mediaVariantModel->newQuery()->create($data);
    }

    public function deleteByMediaFile(int $mediaFileId): int
    {
        return $this->mediaVariantModel->newQuery()
            ->where('media_file_id', $mediaFileId)
            ->delete();
    }
}
