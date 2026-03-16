<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Promotion\Http\Requests\StoreCartPriceRuleRequest;
use Quicktane\Promotion\Http\Requests\UpdateCartPriceRuleRequest;
use Quicktane\Promotion\Http\Resources\CartPriceRuleResource;
use Quicktane\Promotion\Models\CartPriceRule;
use Quicktane\Promotion\Repositories\CartPriceRuleRepository;
use Quicktane\Promotion\Repositories\CouponRepository;
use Quicktane\Promotion\Repositories\RuleConditionRepository;

class CartPriceRuleController extends Controller
{
    public function __construct(
        private readonly CartPriceRuleRepository $cartPriceRuleRepository,
        private readonly RuleConditionRepository $ruleConditionRepository,
        private readonly CouponRepository $couponRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'is_active' => $request->query('is_active'),
            'search' => $request->query('search'),
        ], fn (mixed $value): bool => $value !== null);

        $cartPriceRules = $this->cartPriceRuleRepository->paginate($filters, $perPage);

        return CartPriceRuleResource::collection($cartPriceRules);
    }

    public function store(StoreCartPriceRuleRequest $request): JsonResponse
    {
        $validatedData = $request->validated();

        $conditionsData = $validatedData['conditions'] ?? [];
        $couponsData = $validatedData['coupons'] ?? [];
        unset($validatedData['conditions'], $validatedData['coupons']);

        $cartPriceRule = $this->cartPriceRuleRepository->create($validatedData);

        if (! empty($conditionsData)) {
            $this->ruleConditionRepository->syncConditions($cartPriceRule->id, $conditionsData);
        }

        foreach ($couponsData as $couponData) {
            $couponData['cart_price_rule_id'] = $cartPriceRule->id;
            $this->couponRepository->create($couponData);
        }

        $cartPriceRule->load('conditions.children', 'coupons');

        return (new CartPriceRuleResource($cartPriceRule))
            ->response()
            ->setStatusCode(201);
    }

    public function show(CartPriceRule $rule): CartPriceRuleResource
    {
        $rule->load('conditions.children', 'coupons');

        return new CartPriceRuleResource($rule);
    }

    public function update(UpdateCartPriceRuleRequest $request, CartPriceRule $rule): CartPriceRuleResource
    {
        $validatedData = $request->validated();

        $conditionsData = $validatedData['conditions'] ?? null;
        $couponsData = $validatedData['coupons'] ?? null;
        unset($validatedData['conditions'], $validatedData['coupons']);

        $cartPriceRule = $this->cartPriceRuleRepository->update($rule, $validatedData);

        if ($conditionsData !== null) {
            $this->ruleConditionRepository->syncConditions($cartPriceRule->id, $conditionsData);
        }

        if ($couponsData !== null) {
            $existingCoupons = $this->couponRepository->findByRule($cartPriceRule->id);
            $existingCouponCodes = $existingCoupons->pluck('code')->toArray();
            $incomingCouponCodes = array_column($couponsData, 'code');

            foreach ($existingCoupons as $existingCoupon) {
                if (! in_array($existingCoupon->code, $incomingCouponCodes, true)) {
                    $this->couponRepository->delete($existingCoupon);
                }
            }

            foreach ($couponsData as $couponData) {
                $couponData['cart_price_rule_id'] = $cartPriceRule->id;

                if (in_array($couponData['code'], $existingCouponCodes, true)) {
                    $existingCoupon = $existingCoupons->firstWhere('code', $couponData['code']);
                    if ($existingCoupon !== null) {
                        $this->couponRepository->update($existingCoupon, $couponData);
                    }
                } else {
                    $this->couponRepository->create($couponData);
                }
            }
        }

        $cartPriceRule->load('conditions.children', 'coupons');

        return new CartPriceRuleResource($cartPriceRule);
    }

    public function destroy(CartPriceRule $rule): JsonResponse
    {
        $this->cartPriceRuleRepository->delete($rule);

        return response()->json(null, 204);
    }
}
