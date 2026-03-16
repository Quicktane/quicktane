<?php

declare(strict_types=1);

namespace Quicktane\Media\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Quicktane\Media\Models\MediaFile;

class MysqlMediaFileRepository implements MediaFileRepository
{
    public function __construct(
        private readonly MediaFile $mediaFileModel,
    ) {}

    public function findById(int $id): ?MediaFile
    {
        return $this->mediaFileModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?MediaFile
    {
        return $this->mediaFileModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->mediaFileModel->newQuery()->latest()->paginate($perPage);
    }

    public function create(array $data): MediaFile
    {
        return $this->mediaFileModel->newQuery()->create($data);
    }

    public function update(MediaFile $mediaFile, array $data): MediaFile
    {
        $mediaFile->update($data);

        return $mediaFile;
    }

    public function delete(MediaFile $mediaFile): bool
    {
        return (bool) $mediaFile->delete();
    }
}
