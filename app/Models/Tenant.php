<?php

declare(strict_types = 1);

namespace App\Models;

//use App\Trait\TenantScopeTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Contracts\Auditable;

class Tenant extends Model implements Auditable
{
    //use TenantScopeTrait;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
    ];

    /**
     * Obtém todos os usuários pertencentes a este inquilino (tenant).
     * Este método estabelece um relacionamento um-para-muitos onde:
     * - Um inquilino pode ter múltiplos usuários
     * - Cada usuário pertence a um inquilino
     * - A chave estrangeira 'tenant_id' na tabela users referencia este inquilino
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
