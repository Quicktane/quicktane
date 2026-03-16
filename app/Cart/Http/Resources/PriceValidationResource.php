<?php

declare(strict_types=1);

namespace App\Cart\Http\Resources;

use App\Cart\DataTransferObjects\PriceValidationResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PriceValidationResource extends JsonResource
{
    /**
     * @param  PriceValidationResult  $resource
     */
    public function __construct(
        public readonly PriceValidationResult $result,
    ) {
        parent::__construct($result);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'is_valid' => $this->result->isValid,
            'changed_items' => $this->result->changedItems,
            'out_of_stock_items' => $this->result->outOfStockItems,
            'insufficient_stock_items' => $this->result->insufficientStockItems,
        ];
    }
}
