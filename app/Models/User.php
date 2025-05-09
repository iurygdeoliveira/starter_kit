<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\BelongsToTenantTrait;
use App\Trait\UuidTrait;
use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable, MustVerifyEmail, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use UuidTrait;
    use BelongsToTenantTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'tenant_id',
        'uuid',

        'name',
        'email',
        'cpf',
        'phone',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'          => 'hashed',
            'created_at'        => 'datetime:d/m/Y H:i',
            'updated_at'        => 'datetime:d/m/Y H:i',
            'email_verified_at' => 'datetime:d/m/Y H:i',
            'suspended_at'      => 'datetime:d/m/Y H:i',
            'suspended_until'   => 'datetime:d/m/Y H:i',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail();
    }

    // RELACIONAMENTOS

    // Cada usuário pertence a um tenant
    // e um tenant pode ter muitos usuários
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // Cada usuário pode ter muitas roles
    // e cada role pode ter muitos usuários
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    // Cada usuário pode ter muitas tasks
    // e cada task pode ter muitos usuários
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(Task::class, 'task_user');
    }

    // Cada usuário pode ter muitos clientes
    // e cada cliente pode ter muitos usuários
    public function clients(): BelongsToMany
    {
        return $this->belongsToMany(Client::class, 'client_user');
    }

    #[\Override]
    public function getRouteKeyName(): string
    {
        return 'uuid';  // Substitua por 'uuid' ou o nome do campo que contém seu UUID
    }

    /**
     * Verifica se o usuário está suspenso.
     * Um usuário é considerado suspenso se a data de suspensão (suspended_until) não for nula
     * e a data atual for menor que a data de suspensão.
     */
    public function suspended(): bool
    {
        return ! is_null($this->suspended_until) && Carbon::now()->lessThan($this->suspended_until);
    }

    public function setCreatedAtAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['created_at'] = $value;
    }

    public function setSuspendedAtAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['suspended_at'] = $value;
    }

    public function setSuspendedUntilAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['suspended_until'] = $value;
    }

    public function setUpdatedAtAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['updated_at'] = $value;
    }
}
