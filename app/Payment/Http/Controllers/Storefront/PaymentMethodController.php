<?php

declare(strict_types=1);

namespace App\Payment\Http\Controllers\Storefront;

use App\Payment\Contracts\PaymentFacade;
use App\Payment\Http\Resources\PaymentMethodResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class PaymentMethodController extends Controller
{
    public function __construct(
        private readonly PaymentFacade $paymentFacade,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $paymentMethods = $this->paymentFacade->getActivePaymentMethods();

        return PaymentMethodResource::collection($paymentMethods);
    }
}
