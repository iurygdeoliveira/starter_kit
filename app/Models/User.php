<?php

declare(strict_types = 1);

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Trait\TenantModelTrait;
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
    use TenantModelTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'email',
        'cpf',
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
     * Verifica se o usuário está suspenso.
     * Um usuário é considerado suspenso se a data de suspensão (suspended_until) não for nula
     * e a data atual for menor que a data de suspensão.
     */
    public function suspended(): bool
    {
        return ! is_null($this->suspended_until) && Carbon::now()->lessThan($this->suspended_until);
    }

    private function formatDate(\DateTimeInterface | \Carbon\WeekDay | \Carbon\Month | string | int | float | null $value): string
    {
        return Carbon::parse($value)->toDateTimeString();
    }

    public function setCreatedAtAttribute($value): void
    {
        $this->attributes['created_at'] = $this->formatDate($value);
    }

    public function setSuspendedAtAttribute($value): void
    {
        $this->attributes['suspended_at'] = $this->formatDate($value);
    }

    public function setSuspendedUntilAttribute($value): void
    {
        $this->attributes['suspended_until'] = $this->formatDate($value);
    }

    public function setUpdatedAtAttribute($value): void
    {
        $this->attributes['updated_at'] = $this->formatDate($value);
    }
}
