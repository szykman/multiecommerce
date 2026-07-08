<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\StoreDomain;
use App\Services\TenantManager;

class TenantMiddleware
{

public function handle(
    Request $request,
    Closure $next
) {
    $domain = $request->getHost();

    $storeDomain = StoreDomain::with('store')
        ->where('domain', $domain)
        ->first();

    if (!$storeDomain) {
        abort(404, 'Store not found');
    }

    app(\App\Services\TenantManager::class)
        ->setStore($storeDomain->store);

    return $next($request);
}

}
