<?php

declare(strict_types=1);

namespace App\Customer\Repositories;

use App\Customer\Models\CustomerSegment;
use Illuminate\Support\Collection;

class MysqlCustomerSegmentRepository implements CustomerSegmentRepository
{
    public function __construct(
        private readonly CustomerSegment $segmentModel,
    ) {}

    public function findById(int $id): ?CustomerSegment
    {
        return $this->segmentModel->newQuery()->find($id);
    }

    public function findByUuid(string $uuid): ?CustomerSegment
    {
        return $this->segmentModel->newQuery()->where('uuid', $uuid)->first();
    }

    public function all(): Collection
    {
        return $this->segmentModel->newQuery()->get();
    }

    public function allActive(): Collection
    {
        return $this->segmentModel->newQuery()->where('is_active', true)->get();
    }

    public function create(array $data): CustomerSegment
    {
        return $this->segmentModel->newQuery()->create($data);
    }

    public function update(CustomerSegment $segment, array $data): CustomerSegment
    {
        $segment->update($data);

        return $segment;
    }

    public function delete(CustomerSegment $segment): bool
    {
        return (bool) $segment->delete();
    }
}
