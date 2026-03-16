<?php

declare(strict_types=1);

namespace App\Payment\Http\Controllers\Storefront;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Quicktane\Core\Pipeline\Pipeline;

class PaymentCallbackController extends Controller
{
    public function __construct(
        private readonly Pipeline $pipeline,
    ) {}

    public function __invoke(Request $request, string $gateway): JsonResponse
    {
        $token = $request->query('token');

        if (! is_string($token) || $token === '') {
            return response()->json(['message' => 'Missing pipeline token.'], 400);
        }

        $result = $this->pipeline->resume($token, [
            'payment_confirmed' => true,
            'gateway' => $gateway,
            'callback_data' => $request->all(),
        ]);

        if ($result->isSuspended) {
            return response()->json([
                'message' => 'Payment processing requires additional action.',
                'redirect_url' => $result->redirectUrl,
            ]);
        }

        return response()->json([
            'message' => 'Payment processed successfully.',
            'data' => $result->data,
        ]);
    }
}
