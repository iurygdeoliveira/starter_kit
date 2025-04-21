<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    public function definition(): array
    {
        return [
            'name'  => fake('pt_BR')->unique()->company(),
            'cnpj'  => fake('pt_BR')->unique()->cnpj(),
            'uuid'  => fake('pt_BR')->unique()->uuid(),
            'email' => fake('pt_BR')->unique()->companyEmail(),
        ];
    }
}
