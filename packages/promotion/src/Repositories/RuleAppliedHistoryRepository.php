<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Support\Collection;
use Quicktane\Promotion\Models\RuleAppliedHistory;

interface RuleAppliedHistoryRepository
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): RuleAppliedHistory;

    public function findByOrder(int $orderId): Collection;
}
