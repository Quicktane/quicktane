<?php

declare(strict_types=1);

namespace App\Payment\Http\Controllers;

use App\Payment\Http\Requests\StorePaymentMethodRequest;
use App\Payment\Http\Requests\UpdatePaymentMethodRequest;
use App\Payment\Http\Resources\PaymentMethodResource;
use App\Payment\Models\PaymentMethod;
use App\Payment\Repositories\PaymentMethodRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class PaymentMethodController extends Controller
{
    public function __construct(
        private readonly PaymentMethodRepository $paymentMethodRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['is_active', 'search']);

        $paymentMethods = $this->paymentMethodRepository->paginate($filters, $perPage);

        return PaymentMethodResource::collection($paymentMethods);
    }

    public function store(StorePaymentMethodRequest $request): JsonResponse
    {
        $paymentMethod = $this->paymentMethodRepository->create($request->validated());

        return (new PaymentMethodResource($paymentMethod))
            ->response()
            ->setStatusCode(201);
    }

    public function show(PaymentMethod $paymentMethod): PaymentMethodResource
    {
        return new PaymentMethodResource($paymentMethod);
    }

    public function update(UpdatePaymentMethodRequest $request, PaymentMethod $paymentMethod): PaymentMethodResource
    {
        $paymentMethod = $this->paymentMethodRepository->update($paymentMethod, $request->validated());

        return new PaymentMethodResource($paymentMethod);
    }

    public function destroy(PaymentMethod $paymentMethod): JsonResponse
    {
        $this->paymentMethodRepository->delete($paymentMethod);

        return response()->json(null, 204);
    }
}
