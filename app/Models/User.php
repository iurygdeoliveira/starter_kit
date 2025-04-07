<?php

declare(strict_types = 1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
            'suspended_until'   => 'datetime:d/m/Y H:i',
            'created_at'        => 'datetime:d/m/Y H:i',
            'updated_at'        => 'datetime:d/m/Y H:i',
            'deleted_at'        => 'datetime:d/m/Y H:i',
        ];
    }

    public function setCreatedAtAttribute(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): void
    {
        $this->attributes['created_at'] = Carbon::parse($value)->toDateTimeString();
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

    #[\Override]
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder): void {
            if (app()->has('currentTenant')) {
                $builder->where('tenant_id', app('currentTenant')->id);
            }
        });

        static::creating(function ($user): void {
            if (app()->has('currentTenant')) {
                $user->tenant_id = app('currentTenant')->id;
            }
        });
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
}
