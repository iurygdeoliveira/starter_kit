<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       // dd(Auth::user());
        if (Auth::check() && Auth::user()->tenant_id && !session('tenant')) {
            // Armazena dados do tenant na sessão
            session([
                'tenant' => [
                    'id'    => Auth::user()->tenant_id,
                    'name'  => Auth::user()->tenant->name,
                    'cnpj'  => Auth::user()->tenant->cnpj,
                    'email' => Auth::user()->tenant->email,
                    'uuid'  => Auth::user()->tenant->uuid,
                ],
            ]);
        }

        return $next($request);
    }
}
