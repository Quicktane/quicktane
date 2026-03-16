<?php

declare(strict_types=1);

namespace App\Payment\Http\Controllers;

use App\Payment\Http\Resources\TransactionResource;
use App\Payment\Models\Transaction;
use App\Payment\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class TransactionController extends Controller
{
    public function __construct(
        private readonly TransactionRepository $transactionRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['order_id', 'status', 'type', 'payment_method_code']);

        $transactions = $this->transactionRepository->paginate($filters, $perPage);

        return TransactionResource::collection($transactions);
    }

    public function show(Transaction $transaction): TransactionResource
    {
        $transaction->load('logs');

        return new TransactionResource($transaction);
    }
}
