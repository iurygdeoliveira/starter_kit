<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

// Objetivo da trait:
// Esta trait garante que, ao criar ou atualizar registros, o campo tenant_id seja automaticamente preenchido
// com o tenant_id do usuário autenticado. Dessa forma, assegura que os dados estejam sempre associados ao tenant correto,
// mantendo a integridade e isolamento dos dados em um ambiente multitenancy.

trait BelongsToTenantTrait
{
    public static function bootBelongsToTenantTrait(): void
    {
        // Pula a execução se estiver rodando comandos de migração ou seeding
        if (app()->runningInConsole() &&
            (in_array('migrate', $_SERVER['argv']) ||
             in_array('db:seed', $_SERVER['argv']) ||
             in_array('migrate:fresh', $_SERVER['argv']))) {
            return;
        }

        static::creating(function ($model): void {
            // Lança exceção se o usuário NÃO está autenticado ou não tem tenant_id
            if (! Auth::check() || ! Auth::user()->tenant_id) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
            }

            $model->tenant_id = Auth::user()->tenant_id;
        });

        static::updating(function ($model): void {
            // Lança exceção se o usuário NÃO está autenticado ou não tem tenant_id
            if (! Auth::check() || ! Auth::user()->tenant_id) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
            }

            if ($model->isDirty('tenant_id')) {
                $model->tenant_id = $model->getOriginal('tenant_id');
            }
        });

        static::deleting(function (): void {
            // Lança exceção se o usuário NÃO está autenticado ou não tem tenant_id
            if (! Auth::check() || ! Auth::user()->tenant_id) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
            }
        });
    }
}
