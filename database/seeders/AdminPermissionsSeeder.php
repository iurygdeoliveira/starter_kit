<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class AdminPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Busca a role de Administração (ou cria se não existir)
        $adminRole = Role::firstOrCreate(['name' => RoleEnum::Administração->value]);

        // Busca todas as permissões
        $allPermissions = Permission::all();

        // Atribui todas as permissões à role de Administração
        $adminRole->permissions()->sync($allPermissions->pluck('id')->toArray());
    }
}
