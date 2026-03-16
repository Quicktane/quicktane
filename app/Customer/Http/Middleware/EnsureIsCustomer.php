<?php

declare(strict_types=1);

namespace App\Customer\Http\Middleware;

use App\Customer\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() instanceof Customer) {
            abort(403, 'Access denied.');
        }

        return $next($request);
    }
}
