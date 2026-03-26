<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetSessionDomainFromRequestHost
{
    /**
     * Scope the session cookie to the current host.
     *
     * This avoids sharing sessions across central/tenant domains (and across tenant
     * subdomains) while still supporting local development.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        // Browsers treat localhost specially; a cookie with an explicit domain attribute
        // often won't be stored reliably.
        $domain = null;
        if (
            $host !== 'localhost'
            && ! str_ends_with($host, '.localhost')
            && ! filter_var($host, FILTER_VALIDATE_IP)
        ) {
            $domain = $host;
        }

        config(['session.domain' => $domain]);

        return $next($request);
    }
}

