<?php

declare(strict_types=1);

namespace App\Cart\Http\Controllers\Storefront;

use App\Cart\Contracts\CartFacade;
use App\Cart\Http\Requests\AddCartItemRequest;
use App\Cart\Http\Requests\UpdateCartItemRequest;
use App\Cart\Http\Resources\CartDetailResource;
use App\Cart\Http\Resources\CartItemResource;
use App\Cart\Http\Resources\PriceValidationResource;
use App\Cart\Models\Cart;
use App\Cart\Models\CartItem;
use App\Cart\Services\CartService;
use App\Customer\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function __construct(
        private readonly CartFacade $cartFacade,
        private readonly CartService $cartService,
    ) {}

    public function show(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

        if ($cart === null) {
            return response()->json(['data' => null]);
        }

        $cart->load('items');

        return (new CartDetailResource($cart))->response();
    }

    public function addItem(AddCartItemRequest $request): JsonResponse
    {
        $cart = $this->resolveOrCreateCart($request);

        $cartItem = $this->cartFacade->addItem(
            $cart->id,
            $request->validated('product_uuid'),
            (int) $request->validated('quantity'),
            $request->validated('options'),
        );

        $cart->refresh()->load('items');

        $response = (new CartDetailResource($cart))->response()->setStatusCode(201);

        if ($cart->guest_token !== null) {
            $response->header('X-Cart-Token', $cart->guest_token);
        }

        return $response;
    }

    public function updateItem(UpdateCartItemRequest $request, CartItem $item): JsonResponse
    {
        $cartItem = $this->cartFacade->updateItemQuantity(
            $item->uuid,
            (int) $request->validated('quantity'),
        );

        return (new CartItemResource($cartItem))->response();
    }

    public function removeItem(CartItem $item): JsonResponse
    {
        $this->cartFacade->removeItem($item->uuid);

        return response()->json(null, 204);
    }

    public function clear(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

        if ($cart !== null) {
            $this->cartFacade->clearCart($cart->id);
        }

        return response()->json(null, 204);
    }

    public function validate(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

        if ($cart === null) {
            return response()->json(['message' => 'No active cart found.'], 404);
        }

        $result = $this->cartFacade->revalidatePrices($cart->id);

        return (new PriceValidationResource($result))->response();
    }

    public function confirmPrices(Request $request): JsonResponse
    {
        $cart = $this->resolveCart($request);

        if ($cart === null) {
            return response()->json(['message' => 'No active cart found.'], 404);
        }

        $this->cartFacade->confirmPriceChanges($cart->id);

        return response()->json(['message' => 'Prices confirmed.']);
    }

    private function resolveCart(Request $request): ?Cart
    {
        /** @var Customer|null $customer */
        $customer = $request->user();

        if ($customer !== null) {
            return $this->cartFacade->getActiveCart($customer->id);
        }

        $guestToken = $request->header('X-Cart-Token');

        if ($guestToken !== null) {
            return $this->cartFacade->getActiveGuestCart($guestToken);
        }

        return null;
    }

    private function resolveOrCreateCart(Request $request): Cart
    {
        /** @var Customer|null $customer */
        $customer = $request->user();

        $storeId = (int) $request->input('store_id');
        $currencyCode = (string) $request->input('currency_code', 'USD');

        if ($customer !== null) {
            return $this->cartService->getOrCreateCart($customer->id, $storeId, $currencyCode);
        }

        $guestToken = $request->header('X-Cart-Token') ?? (string) Str::uuid();

        return $this->cartService->getOrCreateGuestCart($guestToken, $storeId, $currencyCode);
    }
}
