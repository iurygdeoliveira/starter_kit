<?php

declare(strict_types = 1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Trait\BelongsToTenantTrait;
use App\Trait\UuidTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use UuidTrait;
    use BelongsToTenantTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'cpf',
        'uuid',
        'phone',
        'password',
        'tenant_id',
        'verified',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password'        => 'hashed',
            'created_at'      => 'datetime:d/m/Y H:i',
            'updated_at'      => 'datetime:d/m/Y H:i',
            'suspended_at'    => 'datetime:d/m/Y H:i',
            'suspended_until' => 'datetime:d/m/Y H:i',
        ];
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
 * Define o campo a ser usado como identificador nas rotas.
 */
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
