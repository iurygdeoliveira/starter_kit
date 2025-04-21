<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\TenantModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Permission extends Model implements Auditable
{
    //
    use TenantModelTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['name'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_permission');
    }
}
