<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Enums\Role as EnumsRole;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
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

        // Obtém apenas a role de Administração
        $adminRole = Role::where('name', EnumsRole::Administracao->value)->first();

        // Cria 1 admin para cada tenant
        $tenants->each(function ($tenant) use ($adminRole): void {
            $admin = User::factory()
                ->forTenant($tenant)
                ->create();

            // Associa a role de admin ao usuário
            $admin->roles()->attach([$adminRole->id]);
        });
    }
}
