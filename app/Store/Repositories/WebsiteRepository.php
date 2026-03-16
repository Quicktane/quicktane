<?php

declare(strict_types=1);

namespace App\Store\Repositories;

use App\Store\Models\Website;
use Illuminate\Support\Collection;

interface WebsiteRepository
{
    public function findById(int $id): ?Website;

    public function findByUuid(string $uuid): ?Website;

    public function findByCode(string $code): ?Website;

    public function all(): Collection;

    public function create(array $data): Website;

    public function update(Website $website, array $data): Website;

    public function delete(Website $website): bool;
}
