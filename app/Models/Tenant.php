<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\SupportUserTrait;
use App\Trait\UuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\Auditable;

class Tenant extends Model implements Auditable
{
    use UuidTrait;
    use SupportUserTrait;
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'name',
        'cnpj',
        'email',
        'phone',
        'uuid',
    ];

    #[\Override]
    protected static function booted()
    {
        static::addGlobalScope('tenant', function ($query): void {
            // Verifica primeiro se existe um usuário autenticado
            if (! Auth::check()) {
                return; // Retorna sem aplicar o filtro durante seed/migrations
            }

            // Verifica se NÃO é o usuário de suporte
            if (! static::isSupportUser()) {
                $query->where('id', Auth::user()->tenant_id);
            }
        });
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
