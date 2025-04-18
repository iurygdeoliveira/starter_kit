<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

// Objetivo da trait:
// Esta trait adiciona um escopo global para todas as consultas realizadas no modelo que a utiliza.
// O escopo filtra os registros com base no tenant_id do usuário autenticado. Assim, garante que o usuário
// só acesse os registros relacionados ao seu próprio tenant, implementando um controle básico de multitenancy.

trait TenantScopeTrait
{
    public static function bootTenantScopeTrait(): void
    {
        static::addGlobalScope('tenant_id', function (Builder $builder): void {
            if (! Auth::user()) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
            }

            $builder->where('tenant_id', Auth::user()->tenant_id);
        });
    }
}
