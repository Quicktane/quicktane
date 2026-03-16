<?php

declare(strict_types=1);

namespace App\Order\Facades;

use App\Order\Contracts\CreditMemoFacade as CreditMemoFacadeContract;
use App\Order\Models\CreditMemo;
use App\Order\Services\CreditMemoService;
use Illuminate\Support\Collection;

class CreditMemoFacade implements CreditMemoFacadeContract
{
    public function __construct(
        private readonly CreditMemoService $creditMemoService,
    ) {}

    public function createCreditMemo(int $orderId, array $data = []): CreditMemo
    {
        return $this->creditMemoService->createCreditMemo($orderId, $data);
    }

    public function getCreditMemosByOrder(int $orderId): Collection
    {
        return $this->creditMemoService->getCreditMemosByOrder($orderId);
    }
}
