<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use App\Models\Admin;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Executa o seeder para criar usuários de exemplo.
     * Cria usuários associados aos tenants existentes.
     */
    public function run(): void
    {
        // Obtém todos os tenants ou cria um se não existir nenhum
        $tenants = Tenant::where('name', '!=', 'Elshamah Tecnologia LTDA')->get();

        // Garantir que todas as roles do enum existem no banco de dados
        foreach (RoleEnum::cases() as $roleEnum) {
            Role::firstOrCreate(['name' => $roleEnum->value]);
        }

        // Obtém todas as roles
        $roles = Role::all();

        // Cria 1 admin para cada tenant
        $tenants->each(function ($tenant) use ($roles): void {
            $admin = Admin::factory()
                ->forTenant($tenant)
                ->create();

            // Associa todas as roles ao admin
            $admin->roles()->attach($roles->pluck('id')->toArray());
        });
    }
}
