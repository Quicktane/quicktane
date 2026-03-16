<?php

declare(strict_types=1);

namespace Quicktane\Promotion\Http\Controllers\Storefront;

use App\Cart\Contracts\CartFacade;
use App\Cart\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Quicktane\Promotion\Contracts\PromotionFacade;
use Quicktane\Promotion\Http\Requests\ApplyCouponRequest;
use Quicktane\Promotion\Http\Resources\PromotionResultResource;

class CouponController extends Controller
{
    public function __construct(
        private readonly PromotionFacade $promotionFacade,
        private readonly CartFacade $cartFacade,
    ) {}

    public function applyCoupon(ApplyCouponRequest $request): JsonResponse
    {
        $couponCode = $request->validated('code');
        $cartUuid = $request->validated('cart_uuid');

        $cart = $this->cartFacade->getCartWithItems(0);

        $cartByUuid = Cart::where('uuid', $cartUuid)->first();

        if ($cartByUuid === null) {
            return response()->json([
                'message' => 'Cart not found.',
            ], 404);
        }

        $customerId = $cartByUuid->customer_id;

        $couponValidationResult = $this->promotionFacade->validateCoupon($couponCode, $cartByUuid->id, $customerId);

        if (! $couponValidationResult->isValid) {
            return response()->json([
                'message' => $couponValidationResult->errorMessage,
            ], 422);
        }

        $promotionResult = $this->promotionFacade->applyRules($cartByUuid->id, $customerId, $couponCode);

        return (new PromotionResultResource($promotionResult))
            ->response()
            ->setStatusCode(200);
    }

    public function removeCoupon(Request $request): JsonResponse
    {
        return response()->json([
            'message' => 'Coupon removed.',
        ]);
    }
}
