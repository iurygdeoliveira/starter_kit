<?php

declare(strict_types = 1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Trait\BelongsToTenantTrait;
use App\Trait\TenantScopeTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;

class User extends Authenticatable implements Auditable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use TenantScopeTrait;
    use BelongsToTenantTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:d/m/Y H:i',
            'password'          => 'hashed',
            'created_at'        => 'datetime:d/m/Y H:i',
            'updated_at'        => 'datetime:d/m/Y H:i',
            'suspended_at'      => 'datetime:d/m/Y H:i',
            'suspended_until'   => 'datetime:d/m/Y H:i',
        ];
    }

    /**
     * Define o relacionamento com o inquilino (tenant) ao qual este usuário pertence.
     * Este método estabelece um relacionamento muitos-para-um onde:
     * - Muitos usuários podem pertencer a um único tenant
     * - O campo tenant_id na tabela users é a chave estrangeira
     * - Cada usuário só pode pertencer a um tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
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
        $this->attributes['created_at'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setSuspendedAtAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['suspended_at'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setSuspendedUntilAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['suspended_until'] = Carbon::parse($value)->toDateTimeString();
    }

    public function setUpdatedAtAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['updated_at'] = Carbon::parse($value)->toDateTimeString();
    }
}
