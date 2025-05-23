<?php

declare(strict_types = 1);

namespace App\Models;

use App\Enums\Activity;
use App\Enums\Regime;
use App\Trait\BelongsToTenantTrait;
use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use OwenIt\Auditing\Contracts\Auditable;

class Client extends Authenticatable implements Auditable
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
        'user',
        'email',
        'password',
        'email_verified_at',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'   => 'hashed',
            'activity'   => Activity::class,
            'regime'     => Regime::class,
            'created_at' => 'datetime:d/m/Y H:i',
            'updated_at' => 'datetime:d/m/Y H:i',
        ];
    }

    #[\Override]
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
        return $this->belongsToMany(Role::class, 'client_role');
    }

    // Cada cliente pode ter muitos usuários
    // e cada usuário pode ter muitos clientes
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'client_user');
    }
}
