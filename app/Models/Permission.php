<?php

declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Permission extends Model implements Auditable
{
    //
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name'];

    public $timestamps = false;

    // Cada permissão pode ter muitas roles
    // e cada role pode ter muitas permissões
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }

    // Cada permissão pode ter muitas tarefas
    // e cada tarefa pode ter muitas permissões
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'permission_task');
    }
}
