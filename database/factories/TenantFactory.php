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
            'crc'   => $this->crcFaker(),
            'uuid'  => fake('pt_BR')->unique()->uuid(),
            'email' => fake('pt_BR')->unique()->companyEmail(),
            'phone' => fake('pt_BR')->unique()->phoneNumber(),
        ];
    }

    public function crcFaker(): string
    {
        $faker = \Faker\Factory::create('pt_BR');

        // Gerar CRC no formato UF-NNNNNN/O-D (ex: SP-123456/O-7)
        $crc = function () use ($faker) {
            $estados = ['AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
                'MG', 'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN',
                'RO', 'RR', 'RS', 'SC', 'SE', 'SP', 'TO'];

            $uf        = $faker->randomElement($estados);
            $numero    = str_pad((string)$faker->numberBetween(1, 999999), 6, '0', STR_PAD_LEFT);
            $categoria = $faker->randomElement(['O', 'T']); // O=Contador, T=Técnico
            $digito    = $faker->numberBetween(1, 9);

            return "{$uf}-{$numero}/{$categoria}-{$digito}";
        };

        return $crc();
    }
}
