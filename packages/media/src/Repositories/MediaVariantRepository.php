<?php

declare(strict_types=1);

namespace Quicktane\Media\Repositories;

use Illuminate\Support\Collection;
use Quicktane\Media\Models\MediaVariant;

interface MediaVariantRepository
{
    public function findById(int $id): ?MediaVariant;

    public function getByMediaFile(int $mediaFileId): Collection;

    public function findByMediaFileAndVariant(int $mediaFileId, string $variantName): ?MediaVariant;

    public function create(array $data): MediaVariant;

    public function deleteByMediaFile(int $mediaFileId): int;
}
