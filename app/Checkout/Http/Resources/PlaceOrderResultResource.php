<?php

declare(strict_types=1);

namespace App\Checkout\Http\Resources;

use App\Checkout\DataTransferObjects\PlaceOrderResult;
use App\Order\Http\Resources\OrderResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlaceOrderResultResource extends JsonResource
{
    public function __construct(
        private readonly PlaceOrderResult $placeOrderResult,
    ) {
        parent::__construct($placeOrderResult);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'success' => $this->placeOrderResult->success,
            'order' => $this->placeOrderResult->order !== null
                ? new OrderResource($this->placeOrderResult->order)
                : null,
            'suspended' => $this->placeOrderResult->suspended,
            'pipeline_token' => $this->placeOrderResult->pipelineToken,
            'redirect_url' => $this->placeOrderResult->redirectUrl,
            'errors' => $this->placeOrderResult->errors,
        ];
    }
}
