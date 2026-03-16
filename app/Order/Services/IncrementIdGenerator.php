<?php

declare(strict_types=1);

namespace App\Order\Services;

use App\Order\Models\CreditMemo;
use App\Order\Models\Invoice;
use App\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;

class IncrementIdGenerator
{
    private const string ORDER_SEQUENCE_KEY = 'sequence:order_increment_id';

    private const string INVOICE_SEQUENCE_KEY = 'sequence:invoice_increment_id';

    private const string CREDIT_MEMO_SEQUENCE_KEY = 'sequence:credit_memo_increment_id';

    private const int START_VALUE = 100000001;

    public function nextOrderId(): string
    {
        return $this->nextId(self::ORDER_SEQUENCE_KEY, Order::class);
    }

    public function nextInvoiceId(): string
    {
        return $this->nextId(self::INVOICE_SEQUENCE_KEY, Invoice::class);
    }

    public function nextCreditMemoId(): string
    {
        return $this->nextId(self::CREDIT_MEMO_SEQUENCE_KEY, CreditMemo::class);
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    private function nextId(string $key, string $modelClass): string
    {
        $connection = Redis::connection();

        $exists = (bool) $connection->exists($key);

        if (! $exists) {
            $maxId = (int) $modelClass::query()->max('increment_id');
            $connection->set($key, max($maxId, self::START_VALUE - 1));
        }

        $nextValue = $connection->incr($key);

        return (string) $nextValue;
    }
}
