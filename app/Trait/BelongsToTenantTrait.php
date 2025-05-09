<?php

declare(strict_types = 1);

namespace App\Trait;

// Objetivo da trait:
// Esta trait garante que, ao criar ou atualizar registros, o campo tenant_id seja automaticamente preenchido
// com o tenant_id do usuário autenticado. Dessa forma, assegura que os dados estejam sempre associados ao tenant correto,
// mantendo a integridade e isolamento dos dados em um ambiente multitenancy.

trait BelongsToTenantTrait
{
    use HandlesCreationTrait;
    use HandlesUpdateTrait;
    use HandlesDeletionTrait;
    use FilterByTenantTrait;

    public static function bootBelongsToTenantTrait(): void
    {
        // Pula a execução se estiver rodando comandos de migração ou seeding
        if (app()->runningInConsole() &&
            (in_array('migrate', $_SERVER['argv']) ||
             in_array('db:seed', $_SERVER['argv']) ||
             in_array('migrate:fresh', $_SERVER['argv']))) {
        }
    }
}
