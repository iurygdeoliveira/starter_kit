<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait HandlesDeletionTrait
{
    use LogsModelActionsTrait;

    public static function bootHandlesDeletionTrait(): void
    {
        static::deleting(function ($model): void {
            // Lança exceção se o usuário NÃO está autenticado ou não tem tenant_id
            if (! Auth::check() || ! Auth::user()->tenant_id) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
            }

            // Registra ação de exclusão em log
            static::logModelAction($model, 'exclusão');
        });

        static::deleted(function ($model): void {
            static::handleDeleted($model);
        });
    }

    /**
     * Lida com ações pós-exclusão do modelo
     */
    protected static function handleDeleted($model): void
    {
        // Ações após exclusão
        // Exemplo: arquivamento de dados, notificações, limpeza de recursos
    }
}
