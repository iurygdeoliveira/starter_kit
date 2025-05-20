<?php

declare(strict_types = 1);

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name      = fake('pt_BR')->name();
        $nameParts = explode(' ', $name);

        // Pega segundo e terceiro nomes se existirem
        $emailName = '';

        if (isset($nameParts[1])) {
            $emailName .= Str::lower($nameParts[1]);
        }

        if (isset($nameParts[2])) {
            $emailName .= '.' . Str::lower($nameParts[2]);
        }

        // Se não tiver partes suficientes, usa um fallback
        if ($emailName === '' || $emailName === '0') {
            $emailName = Str::lower($nameParts[0]);
        }

        return [
            'uuid'              => fake('pt_BR')->unique()->uuid(),
            'name'              => $name,
            'email'             => $emailName . '@' . fake('pt_BR')->domainName(),
            'cpf'               => fake('pt_BR')->unique()->cpf(),
            'phone'             => fake('pt_BR')->unique()->phoneNumber(),
            'password'          => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'tenant_id'         => Tenant::factory(),
        ];
    }

    // Este método permite associar explicitamente um usuário a um tenant específico durante a criação via factory.
    // Ao receber uma instância do modelo Tenant, ele utiliza o método state para definir o campo 'tenant_id'
    // com o ID do tenant fornecido, garantindo que o usuário criado seja vinculado corretamente ao tenant desejado.
    public function forTenant(Tenant $tenant): static
    {
        return $this->state(fn (array $attributes): array => [
            'tenant_id' => $tenant->id,
        ]);
    }
}
