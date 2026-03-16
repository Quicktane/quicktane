<?php

declare(strict_types=1);

namespace App\Checkout\Http\Controllers;

use App\Cart\Repositories\CartRepository;
use App\Checkout\Contracts\CheckoutFacade;
use App\Checkout\Http\Requests\SetAddressRequest;
use App\Checkout\Http\Requests\SetPaymentMethodRequest;
use App\Checkout\Http\Requests\SetShippingMethodRequest;
use App\Checkout\Http\Requests\StartCheckoutRequest;
use App\Checkout\Http\Resources\CheckoutSessionResource;
use App\Checkout\Http\Resources\CheckoutTotalsResource;
use App\Checkout\Http\Resources\PlaceOrderResultResource;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutFacade $checkoutFacade,
        private readonly CartRepository $cartRepository,
    ) {}

    public function start(StartCheckoutRequest $request): CheckoutSessionResource
    {
        $cart = $this->cartRepository->findByUuid($request->validated('cart_uuid'));

        abort_if($cart === null, 404, 'Cart not found.');

        $session = $this->checkoutFacade->startCheckout(
            $cart->id,
            auth()->user()?->id,
        );

        return new CheckoutSessionResource($session);
    }

    public function session(Request $request): CheckoutSessionResource
    {
        $sessionUuid = (string) $request->query('session_uuid');
        $session = $this->checkoutFacade->getSession($sessionUuid);

        return new CheckoutSessionResource($session);
    }

    public function setShippingAddress(SetAddressRequest $request): CheckoutSessionResource
    {
        $validated = $request->validated();
        $sessionUuid = $validated['session_uuid'];
        unset($validated['session_uuid']);

        $session = $this->checkoutFacade->setShippingAddress($sessionUuid, $validated);

        return new CheckoutSessionResource($session);
    }

    public function setBillingAddress(SetAddressRequest $request): CheckoutSessionResource
    {
        $validated = $request->validated();
        $sessionUuid = $validated['session_uuid'];
        unset($validated['session_uuid']);

        $session = $this->checkoutFacade->setBillingAddress($sessionUuid, $validated);

        return new CheckoutSessionResource($session);
    }

    public function setShippingMethod(SetShippingMethodRequest $request): CheckoutSessionResource
    {
        $session = $this->checkoutFacade->setShippingMethod(
            $request->validated('session_uuid'),
            $request->validated('carrier_code'),
            $request->validated('method_code'),
        );

        return new CheckoutSessionResource($session);
    }

    public function setPaymentMethod(SetPaymentMethodRequest $request): CheckoutSessionResource
    {
        $session = $this->checkoutFacade->setPaymentMethod(
            $request->validated('session_uuid'),
            $request->validated('payment_method_code'),
        );

        return new CheckoutSessionResource($session);
    }

    public function applyCoupon(Request $request): CheckoutSessionResource
    {
        $session = $this->checkoutFacade->applyCoupon(
            (string) $request->input('session_uuid'),
            (string) $request->input('coupon_code'),
        );

        return new CheckoutSessionResource($session);
    }

    public function removeCoupon(Request $request): CheckoutSessionResource
    {
        $session = $this->checkoutFacade->removeCoupon(
            (string) $request->input('session_uuid'),
        );

        return new CheckoutSessionResource($session);
    }

    public function totals(Request $request): CheckoutTotalsResource
    {
        $sessionUuid = (string) $request->query('session_uuid');
        $totals = $this->checkoutFacade->calculateTotals($sessionUuid);

        return new CheckoutTotalsResource($totals);
    }

    public function placeOrder(Request $request): PlaceOrderResultResource
    {
        $sessionUuid = (string) $request->input('session_uuid');
        $result = $this->checkoutFacade->placeOrder($sessionUuid);

        return new PlaceOrderResultResource($result);
    }

    public function resume(Request $request): PlaceOrderResultResource
    {
        $pipelineToken = (string) $request->input('pipeline_token');
        $callbackData = (array) $request->input('callback_data', []);

        $result = $this->checkoutFacade->resumeOrder($pipelineToken, $callbackData);

        return new PlaceOrderResultResource($result);
    }
}
