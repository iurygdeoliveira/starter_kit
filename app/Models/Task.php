<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\BelongsToTenantTrait;
use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Task extends Model implements Auditable
{
    //

    use BelongsToTenantTrait;
    use UuidTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tenant_id',
        'role_id',
        'client_id',
        'uuid',
        'status',
        'name',
        'done',
        'attach',
        'due',
        'time',
        'portal',
        'notes',
        'description',
        'created_at',
        'updated_at',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    //RELACIONAMENTOS

    // Cada tarefa pertence a um tenant
    // e um tenant pode ter muitas tarefas
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // Cada tarefa pertence a uma role
    // e uma role pode ter muitas tarefas
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Cada tarefa pode ter um cliente
    // e um cliente pode ter muitas tarefas
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    // Cada tarefa pode ter muitos permissões
    // e cada permissão pode ter muitas tarefas
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'task_permission');
    }

    // Cada tarefa pode ter muitos usuários
    // e cada usuário pode ter muitas tarefas
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_user');
    }
}
