<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Trait\LoggedUserTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetTenantMiddleware
{
    use LoggedUserTrait;

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->LoggedUser()) {
            // Armazena dados do tenant na sessão
            session([
                'tenant' => [
                    'id'    => Auth::user()->tenant_id,
                    'name'  => Auth::user()->tenant->name,
                    'cnpj'  => Auth::user()->tenant->cnpj,
                    'email' => Auth::user()->tenant->email,
                    'uuid'  => Auth::user()->tenant->uuid,
                    'phone' => Auth::user()->tenant->phone,
                ],
            ]);
        }

        return $next($request);
    }
}
