<?php

declare(strict_types=1);

namespace App\Cart\Http\Controllers;

use App\Cart\Http\Resources\CartDetailResource;
use App\Cart\Http\Resources\CartResource;
use App\Cart\Models\Cart;
use App\Cart\Repositories\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class CartController extends Controller
{
    public function __construct(
        private readonly CartRepository $cartRepository,
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['status', 'customer_id', 'search']);

        $carts = $this->cartRepository->paginate($filters, $perPage);

        return CartResource::collection($carts);
    }

    public function show(Cart $cart): CartDetailResource
    {
        $cart->load('items', 'customer');

        return new CartDetailResource($cart);
    }
}
