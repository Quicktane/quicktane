<?php

declare(strict_types=1);

namespace App\Order\Http\Controllers;

use App\Order\Contracts\CreditMemoFacade;
use App\Order\Contracts\InvoiceFacade;
use App\Order\Contracts\OrderFacade;
use App\Order\Contracts\OrderStatusFacade;
use App\Order\Enums\OrderStatus;
use App\Order\Http\Requests\AddOrderCommentRequest;
use App\Order\Http\Requests\ChangeOrderStatusRequest;
use App\Order\Http\Requests\CreateCreditMemoRequest;
use App\Order\Http\Requests\CreateInvoiceRequest;
use App\Order\Http\Resources\CreditMemoResource;
use App\Order\Http\Resources\InvoiceResource;
use App\Order\Http\Resources\OrderDetailResource;
use App\Order\Http\Resources\OrderHistoryResource;
use App\Order\Http\Resources\OrderResource;
use App\Order\Models\Order;
use App\Order\Repositories\OrderHistoryRepository;
use App\Order\Repositories\OrderRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly OrderFacade $orderFacade,
        private readonly OrderStatusFacade $orderStatusFacade,
        private readonly InvoiceFacade $invoiceFacade,
        private readonly CreditMemoFacade $creditMemoFacade,
        private readonly OrderHistoryRepository $orderHistoryRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['status', 'customer_id', 'search']);

        return OrderResource::collection(
            $this->orderRepository->paginate($filters, $perPage),
        );
    }

    public function show(Order $order): OrderDetailResource
    {
        $order = $this->orderFacade->getOrderWithDetails($order->id);

        return new OrderDetailResource($order);
    }

    public function changeStatus(ChangeOrderStatusRequest $request, Order $order): OrderDetailResource
    {
        $order = $this->orderStatusFacade->changeStatus(
            $order->id,
            OrderStatus::from($request->validated('status')),
            $request->validated('comment'),
            $request->user()?->id,
            (bool) $request->validated('notify_customer', false),
        );

        $order = $this->orderFacade->getOrderWithDetails($order->id);

        return new OrderDetailResource($order);
    }

    public function addComment(AddOrderCommentRequest $request, Order $order): JsonResponse
    {
        $this->orderHistoryRepository->create([
            'order_id' => $order->id,
            'status' => $order->status->value,
            'comment' => $request->validated('comment'),
            'is_customer_notified' => (bool) $request->validated('notify_customer', false),
            'user_id' => $request->user()?->id,
        ]);

        return response()->json(['message' => 'Comment added.']);
    }

    public function history(Order $order): AnonymousResourceCollection
    {
        return OrderHistoryResource::collection(
            $this->orderStatusFacade->getHistory($order->id),
        );
    }

    public function createInvoice(CreateInvoiceRequest $request, Order $order): InvoiceResource
    {
        $invoice = $this->invoiceFacade->createInvoice($order->id);

        return new InvoiceResource($invoice);
    }

    public function createCreditMemo(CreateCreditMemoRequest $request, Order $order): CreditMemoResource
    {
        $creditMemo = $this->creditMemoFacade->createCreditMemo($order->id, $request->validated());

        return new CreditMemoResource($creditMemo);
    }
}
