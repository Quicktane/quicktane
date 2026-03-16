<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Quicktane\Promotion\Models\RuleAppliedHistory;

class MysqlRuleAppliedHistoryRepository implements RuleAppliedHistoryRepository
{
    public function __construct(
        private readonly RuleAppliedHistory $ruleAppliedHistoryModel,
    ) {}

    public function create(array $data): RuleAppliedHistory
    {
        $data['created_at'] = Carbon::now();

        return $this->ruleAppliedHistoryModel->newQuery()->create($data);
    }

    public function findByOrder(int $orderId): Collection
    {
        return $this->ruleAppliedHistoryModel->newQuery()
            ->where('order_id', $orderId)
            ->with('rule')
            ->get();
    }
}
