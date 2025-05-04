<?php

declare(strict_types = 1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create(['name' => 'manage']);
        Role::create(['name' => 'create']);
        Role::create(['name' => 'edit']);
        Role::create(['name' => 'view']);
        Role::create(['name' => 'delete']);
    }
}
