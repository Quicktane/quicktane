<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\Website;
use Illuminate\Support\Collection;

class MysqlWebsiteRepository implements WebsiteRepository
{
    public function __construct(
        private readonly Website $websiteModel,
    ) {}

    public function findById(int $id): ?Website
    {
        return $this->websiteModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?Website
    {
        return $this->websiteModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function findByCode(string $code): ?Website
    {
        return $this->websiteModel->newQuery()->where('code', $code)->first();
    }

    public function all(): Collection
    {
        return $this->websiteModel->newQuery()->orderBy('sort_order')->get();
    }

    public function create(array $data): Website
    {
        return $this->websiteModel->newQuery()->create($data);
    }

    public function update(Website $website, array $data): Website
    {
        $website->update($data);

        return $website;
    }

    public function delete(Website $website): bool
    {
        return (bool) $website->delete();
    }
}
