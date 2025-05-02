<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'administrar']);
        Permission::create(['name' => 'criar']);
        Permission::create(['name' => 'editar']);
        Permission::create(['name' => 'visualizar']);
        Permission::create(['name' => 'apagar']);
    }
}
