<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
    ];

    public $timestamps = false;

    // Cada role pode ter muitas tarefas
    // e cada tarefa pertence a uma role
    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    // Cada role pode ter muitas permissões
    // e cada permissão pode ter muitas roles
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    // Cada role pode ter muitos usuários
    // e cada usuário pode ter muitas roles
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'role_user');
    }

    // Cada role pode ter muitos clientes
    // e cada cliente pode ter muitas roles
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_role');
    }
}
