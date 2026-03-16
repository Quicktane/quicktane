<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\CustomerSegment;
use Illuminate\Support\Collection;

interface CustomerSegmentRepository
{
    public function findById(int $id): ?CustomerSegment;

    public function findByUuid(string $uuid): ?CustomerSegment;

    public function all(): Collection;

    public function allActive(): Collection;

    public function create(array $data): CustomerSegment;

    public function update(CustomerSegment $segment, array $data): CustomerSegment;

    public function delete(CustomerSegment $segment): bool;
}
