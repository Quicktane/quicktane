<?php

declare(strict_types=1);

namespace App\Directory\Http\Controllers\Storefront;

use App\Directory\Contracts\CurrencyFacade;
use App\Directory\Http\Resources\CurrencyResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class CurrencyController extends Controller
{
    public function __construct(
        private readonly CurrencyFacade $currencyFacade,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $storeViewId = $request->query('store_view_id');

        if ($storeViewId !== null) {
            $currencies = $this->currencyFacade->availableForStoreView((int) $storeViewId);
        } else {
            $currencies = $this->currencyFacade->listCurrencies(activeOnly: true);
        }

        return response()->json([
            'data' => CurrencyResource::collection($currencies),
        ]);
    }
}
