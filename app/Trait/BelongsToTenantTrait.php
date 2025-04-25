<?php

declare(strict_types = 1);

namespace App\Trait;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        static::updating(function ($model): void {
            // Lança exceção se o usuário NÃO está autenticado ou não tem tenant_id
            if (! Auth::check() || ! Auth::user()->tenant_id) {
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

        static::deleting(function ($model): void {
            // Lança exceção se o usuário NÃO está autenticado ou não tem tenant_id
            if (! Auth::check() || ! Auth::user()->tenant_id) {
                throw new UnauthorizedHttpException('Bearer', 'Operação não permitida: Usuário não autenticado');
            }

            // Registra ação de exclusão em log
            static::logModelAction($model, 'exclusão');
        });

        static::deleted(function ($model): void {
            // Executa ações pós-exclusão
            static::handleDeleted($model);
        });
    }

    /**
     * Registra operações de modelo em log com contexto do tenant
     */
    protected static function logModelAction($model, string $action): void
    {
        $className = class_basename($model);
        $tenantId  = $model->tenant_id ?? (Auth::check() ? Auth::user()->tenant_id : null);
        $userId    = Auth::check() ? Auth::user()->id : 'Sistema';

        Log::info("{$action} de {$className} ID:{$model->id} pelo usuário {$userId} no tenant {$tenantId}");
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

    /**
     * Lida com ações pós-exclusão do modelo
     */
    protected static function handleDeleted($model): void
    {
        // Ações após exclusão
        // Exemplo: arquivamento de dados, notificações, limpeza de recursos
    }
}
