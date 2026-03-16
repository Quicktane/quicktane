<?php

declare(strict_types=1);

namespace App\Order\Contracts;

use App\Order\Models\CreditMemo;
use Illuminate\Support\Collection;

interface CreditMemoFacade
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createCreditMemo(int $orderId, array $data = []): CreditMemo;

    public function getCreditMemosByOrder(int $orderId): Collection;
}
