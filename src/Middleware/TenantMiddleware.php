<?php
namespace Digitools\MultiTenancy\Middleware;

use Closure;
use Digitools\MultiTenancy\Models\Tenant;

class TenantMiddleware
{
    public function handle($request, Closure $next)
    {
        $host = $request->getHost();
        $tenant = Tenant::where('domain', $host)->first();

        if ($tenant) {
            $tenant->switchDatabase();
        } else {
            abort(404, 'Tenant not found.');
        }

        return $next($request);
    }
}
