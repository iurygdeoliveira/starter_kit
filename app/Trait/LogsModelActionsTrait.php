<?php

declare(strict_types = 1);

namespace App\Trait;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait LogsModelActionsTrait
{
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
}
