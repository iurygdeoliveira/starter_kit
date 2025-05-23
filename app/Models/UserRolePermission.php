<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\BelongsToTenantTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use OwenIt\Auditing\Contracts\Auditable;

class UserRolePermission extends Model implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserRolePermissionFactory> */
    use HasFactory;
    use BelongsToTenantTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['user_id', 'role_id', 'permission_id', 'tenant_id'];

    // RELACIONAMENTOS

    // Cada permissão de usuário-role pertence a um usuário
    // e um usuário pode ter muitas permissões de role
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Cada permissão de usuário-role pertence a uma role
    // e uma role pode ter muitas permissões de usuário
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Cada permissão de usuário-role pertence a uma permissão
    // e uma permissão pode estar associada a muitos usuários-roles
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    // Cada permissão de usuário-role pertence a um tenant
    // e um tenant pode ter muitas permissões de usuário-role
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
