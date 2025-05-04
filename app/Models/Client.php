<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\Activity;
use App\Enums\Regime;
use App\Trait\BelongsToTenantTrait;
use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Model implements Auditable
{
    //
    use BelongsToTenantTrait;
    use UuidTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tenant_id',
        'uuid',

        'name',
        'cnpj',
        'activity',
        'regime',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'activity'   => Activity::class,
            'regime'     => Regime::class,
            'created_at' => 'datetime:d/m/Y H:i',
            'updated_at' => 'datetime:d/m/Y H:i',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // RELACIONAMENTOS

    // Cada cliente pertence a um tenant
    // e um tenant pode ter muitos clientes
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // Cada cliente pode ter muitas tarefas
    // e cada tarefa pode ter um cliente
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Cada cliente pode ter muitas roles
    // e cada role pode ter muitos clientes
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_client');
    }

    // Cada cliente pode ter muitos usuários
    // e cada usuário pode ter muitos clientes
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'client_user');
    }
}
