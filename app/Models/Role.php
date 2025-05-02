<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\BelongsToTenantTrait;
use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends Model implements Auditable
{
    use BelongsToTenantTrait;
    use UuidTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tenant_id',
        'uuid',

        'name',
    ];

    public $timestamps = false;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_role');
    }
}
