<?php

declare(strict_types=1);

namespace App\Order\Http\Controllers\Storefront;

use App\Order\Contracts\OrderFacade;
use App\Order\Http\Resources\OrderDetailResource;
use App\Order\Http\Resources\OrderResource;
use App\Order\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderFacade $orderFacade,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $customer = $request->user();
        $perPage = (int) $request->query('per_page', 15);

        return OrderResource::collection(
            $this->orderFacade->getOrdersByCustomer($customer->id, $perPage),
        );
    }

    public function show(Request $request, Order $order): OrderDetailResource
    {
        $customer = $request->user();

        if ($order->customer_id !== $customer->id) {
            throw new NotFoundHttpException;
        }

        $order = $this->orderFacade->getOrderWithDetails($order->id);

        return new OrderDetailResource($order);
    }
}
