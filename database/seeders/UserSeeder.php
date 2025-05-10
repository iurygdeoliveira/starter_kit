<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Executa o seeder para criar usuários de exemplo.
     * Cria usuários associados aos tenants existentes.
     */
    public function run(): void
    {
        // Obtém todos os tenants ou cria um se não existir nenhum
        $tenants = Tenant::where('name', '!=', 'Elshamah Tecnologia LTDA')->get();

        // Cria 2 usuários para cada tenant
        $tenants->each(function ($tenant): void {
            User::factory()
                ->count(2)
                ->forTenant($tenant)
                ->create();
        });
    }
}
