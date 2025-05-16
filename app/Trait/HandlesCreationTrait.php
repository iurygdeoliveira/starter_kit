<?php

declare(strict_types = 1);

namespace App\Trait;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

trait HandlesCreationTrait
{
    use LogsModelActionsTrait;
    use UserLoogedTrait;

    public static function bootHandlesCreationTrait(): void
    {
        static::creating(function ($model): void {
            // EXCEÇÃO ESPECIAL: Permite criar usuários e administradores sem tenant_id
            if ($model instanceof User) {
                // Se já existir um tenant_id, mantenha-o
                if (Auth::check() && Auth::user()->tenant_id) {
                    $model->tenant_id = Auth::user()->tenant_id;
                }
                // Se não existir, permite a criação sem tenant
                static::logModelAction($model, 'criação de usuário ou administrador');

                return;
            }

            // Para OUTROS modelos, continua exigindo tenant_id
            if (! self::isUserLoggedIn()) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
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
        // Caso especial para quando um Tenant é criado
        if ($model instanceof Tenant && Auth::check()) {
            $user = Auth::user();

            // Associa o Tenant recém-criado ao usuário que o criou
            $user->tenant_id = $model->id;
            $user->save();

            static::logModelAction($model, 'associação de tenant ao usuário');
        }

        // Esta função pode ser sobrescrita por modelos específicos
        // Exemplo para User:
        // - Enviar email de boas-vindas
        // - Configurar permissões padrões
    }
}
