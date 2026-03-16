<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Quicktane\Promotion\Http\Requests\StoreCouponRequest;
use Quicktane\Promotion\Http\Requests\UpdateCouponRequest;
use Quicktane\Promotion\Http\Resources\CouponResource;
use Quicktane\Promotion\Models\Coupon;
use Quicktane\Promotion\Repositories\CouponRepository;

class CouponController extends Controller
{
    public function __construct(
        private readonly CouponRepository $couponRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = array_filter([
            'is_active' => $request->query('is_active'),
            'cart_price_rule_id' => $request->query('cart_price_rule_id'),
            'search' => $request->query('search'),
        ], fn (mixed $value): bool => $value !== null);

        $coupons = $this->couponRepository->paginate($filters, $perPage);

        return CouponResource::collection($coupons);
    }

    public function store(StoreCouponRequest $request): JsonResponse
    {
        $coupon = $this->couponRepository->create($request->validated());

        $coupon->load('rule');

        return (new CouponResource($coupon))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Coupon $coupon): CouponResource
    {
        $coupon->load('rule');

        return new CouponResource($coupon);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): CouponResource
    {
        $coupon = $this->couponRepository->update($coupon, $request->validated());

        $coupon->load('rule');

        return new CouponResource($coupon);
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $this->couponRepository->delete($coupon);

        return response()->json(null, 204);
    }
}
