<?php

declare(strict_types=1);

namespace App\Cart\Repositories;

use App\Cart\Models\Cart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CartRepository
{
    public function findById(int $id): ?Cart;

    public function findByUuid(string $uuid): ?Cart;

    public function findActiveByCustomer(int $customerId): ?Cart;

    public function findActiveByGuestToken(string $guestToken): ?Cart;

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Cart;

    public function update(Cart $cart, array $data): Cart;
}
