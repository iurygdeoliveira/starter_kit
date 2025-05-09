<?php

declare(strict_types = 1);

namespace App\Trait;

use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait HandlesUpdateTrait
{
    use LogsModelActionsTrait;
    use UserLoogedTrait;

    public static function bootHandlesUpdateTrait(): void
    {
        static::updating(function ($model): void {
            // Lança exceção se o usuário NÃO está autenticado ou não tem tenant_id
            if (! self::isUserLoggedIn()) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
            }

            if ($model->isDirty('tenant_id')) {
                $model->tenant_id = $model->getOriginal('tenant_id');
            }

            // Registra ação de atualização em log
            static::logModelAction($model, 'atualização');
        });

        static::updated(function ($model): void {
            // Executa ações pós-atualização
            static::handleUpdated($model);
        });
    }

    /**
     * Lida com ações pós-atualização do modelo
     */
    protected static function handleUpdated($model): void
    {
        // Esta função pode ser sobrescrita por modelos específicos
        // Exemplo para User:
        // - Enviar email de notificação de alteração
        // - Atualizar permissões
    }
}
