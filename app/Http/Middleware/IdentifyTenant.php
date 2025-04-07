<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host   = $request->getHost(); // pega o domínio atual
        $tenant = Tenant::where('domain', $host)->first();

        if (! $tenant) {
            abort(404, 'Tenant não encontrado.');
        }

        // Guarda o tenant atual na aplicação
        app()->instance('currentTenant', $tenant);

        return $next($request);
    }
}
