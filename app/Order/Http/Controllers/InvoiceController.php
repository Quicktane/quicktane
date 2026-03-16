<?php

declare(strict_types=1);

namespace App\Order\Http\Controllers;

use App\Order\Http\Resources\InvoiceResource;
use App\Order\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;

class InvoiceController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);

        $query = Invoice::query()->with('order');

        if ($request->has('order_id')) {
            $query->where('order_id', $request->query('order_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $invoices = $query->latest()->paginate($perPage);

        return InvoiceResource::collection($invoices);
    }
}
