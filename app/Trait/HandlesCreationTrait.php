<?php

declare(strict_types = 1);

namespace App\Trait;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait HandlesCreationTrait
{
    public static function bootHandlesCreationTrait(): void
    {
        static::creating(function ($model): void {
            // EXCEÇÃO ESPECIAL: Permite criar usuários sem tenant_id
            if ($model instanceof User) {
                // Se já existir um tenant_id, mantenha-o
                if (Auth::check() && Auth::user()->tenant_id) {
                    $model->tenant_id = Auth::user()->tenant_id;
                }
                // Se não existir, permite a criação sem tenant
                static::logModelAction($model, 'criação de usuário');

                return;
            }

            // Para OUTROS modelos, continua exigindo tenant_id
            if (! Auth::check() || ! Auth::user()->tenant_id) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado ou sem tenant');
            }

            $model->tenant_id = Auth::user()->tenant_id;
            static::logModelAction($model, 'criação');
        });

        static::created(function ($model): void {
            // Executa ações pós-criação
            static::handleCreated($model);
        });
    }

    /**
     * Lida com ações pós-criação do modelo
     */
    protected static function handleCreated($model): void
    {
        // Esta função pode ser sobrescrita por modelos específicos
        // Exemplo para User:
        // - Enviar email de boas-vindas
        // - Configurar permissões padrões
    }
}
