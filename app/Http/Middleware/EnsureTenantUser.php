<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantUser
{
    /**
     * Ensure that the authenticated user belongs to the current tenant.
     * This middleware should be applied AFTER InitializeTenancyByDomain.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return $next($request);
        }

        $currentTenant = tenant();

        if (! $currentTenant) {
            return $next($request);
        }

        $belongsToTenant = $user->tenants()
            ->whereKey($currentTenant->getTenantKey())
            ->exists();

        if (! $belongsToTenant && $user->tenant_id !== $currentTenant->getTenantKey()) {
            abort(403, 'Unauthorized access — you do not belong to this vendor.');
        }

        return $next($request);
    }
}
