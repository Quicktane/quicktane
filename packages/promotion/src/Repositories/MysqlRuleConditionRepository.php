<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Repositories;

use Illuminate\Support\Collection;
use Quicktane\Promotion\Models\RuleCondition;

class MysqlRuleConditionRepository implements RuleConditionRepository
{
    public function __construct(
        private readonly RuleCondition $ruleConditionModel,
    ) {}

    public function findByRule(int $ruleId): Collection
    {
        return $this->ruleConditionModel->newQuery()
            ->where('cart_price_rule_id', $ruleId)
            ->with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get();
    }

    public function syncConditions(int $ruleId, array $conditions): void
    {
        $this->ruleConditionModel->newQuery()
            ->where('cart_price_rule_id', $ruleId)
            ->delete();

        foreach ($conditions as $conditionData) {
            $this->createConditionTree($ruleId, $conditionData, null);
        }
    }

    /**
     * @param  array<string, mixed>  $conditionData
     */
    private function createConditionTree(int $ruleId, array $conditionData, ?int $parentId): void
    {
        $children = $conditionData['children'] ?? [];
        unset($conditionData['children']);

        $conditionData['cart_price_rule_id'] = $ruleId;
        $conditionData['parent_id'] = $parentId;

        $ruleCondition = $this->ruleConditionModel->newQuery()->create($conditionData);

        foreach ($children as $childData) {
            $this->createConditionTree($ruleId, $childData, $ruleCondition->id);
        }
    }
}
