<?php

declare(strict_types=1);

namespace Quicktane\Media\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Media\Models\MediaFile;

interface MediaFileRepository
{
    public function findById(int $id): ?MediaFile;

    public function findByUuid(string $uuid): ?MediaFile;

    public function paginate(int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): MediaFile;

    public function update(MediaFile $mediaFile, array $data): MediaFile;

    public function delete(MediaFile $mediaFile): bool;
}
