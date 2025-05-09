<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait FilterByTenantTrait
{
    use SupportUserTrait;
    use UserLoogedTrait;

    protected static function bootFilterByTenantTrait(): void
    {
        // Se não estiver autenticado, não aplica filtro
        if (! static::isUserLoggedIn()) {
            return;
        }

        // Verifica se é usuário de suporte - estes veem todos os registros
        if (static::isSupportUser()) {
            return;
        }

        // Adiciona um escopo global que filtra resultados por tenant
        static::addGlobalScope('filter-by-tenant', fn (Builder $builder) => $builder
            ->where('tenant_id', Auth::user()->tenant_id));
    }
}
