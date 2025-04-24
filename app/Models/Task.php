<?php

declare(strict_types = 1);

namespace App\Models;

use App\Trait\TenantModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use OwenIt\Auditing\Contracts\Auditable;

class Task extends Model implements Auditable
{
    //
    use TenantModelTrait;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = ['role_id', 'name'];
    public $timestamps = false;

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'task_permission');
    }
}
