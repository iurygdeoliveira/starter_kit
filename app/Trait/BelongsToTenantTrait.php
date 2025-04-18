<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;

// Objetivo da trait:
// Esta trait garante que, ao criar ou atualizar registros, o campo tenant_id seja automaticamente preenchido
// com o tenant_id do usuário autenticado. Dessa forma, assegura que os dados estejam sempre associados ao tenant correto,
// mantendo a integridade e isolamento dos dados em um ambiente multitenancy.

trait BelongsToTenantTrait
{
    public static function bootBelongsToTenantTrait(): void
    {
        static::creating(function ($model): void {
            if (Auth::check() && Auth::user()->tenant_id) {
                $model->tenant_id = Auth::user()->tenant_id;
            }
        });

        // Remover a capacidade de trocar o tenant_id durante atualizações
        static::updating(function ($model): void {
            if ($model->isDirty('tenant_id')) {
                $model->tenant_id = $model->getOriginal('tenant_id');
            }
        });
    }
}
