<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Support\Collection;

interface RuleConditionRepository
{
    public function findByRule(int $ruleId): Collection;

    /**
     * @param  array<int, array<string, mixed>>  $conditions
     */
    public function syncConditions(int $ruleId, array $conditions): void;
}
