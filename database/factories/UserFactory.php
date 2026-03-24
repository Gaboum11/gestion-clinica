<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;
    protected static int $assistantIndex = 0;
    protected static int $doctorIndex = 0;

    private static array $doctorNames = [
        ['name' => 'Alejandra Arriola'],
        ['name' => 'Alisson Quijano'],
        ['name' => 'Melisa Rivas'],
        ['name' => 'Karla Contreras'],
        ['name' => 'Gabriel Martínez'],
        ['name' => 'Diego Baños'],
        ['name' => 'Christian Renderos'],
        ['name' => 'Fernanda Barrera'],
        ['name' => 'Francisco Rauda'],
        ['name' => 'Alberto Ehlerman'],
        ['name' => 'Fiorella Guzman'],
     
    ];

    private static array $assistantNames = [
        ['name' => 'Patricia Rodrígez'],
        ['name' => 'Mario Fernández'],
        ['name' => 'Erick Gutiérrez'],
        ['name' => 'Ana María García'],
        ['name' => 'Javier Ramírez'],
        ['name' => 'Lucia Mendoza'],
        ['name' => 'Rodrigo Castro'],
        ['name' => 'Isabella Moreno'],
        ['name' => 'Diego Reyes'],
        ['name' => 'Beatriz Herrera'],
        ['name' => 'Cristian Flores'],
        ['name' => 'Gabriela López'],
        ['name' => 'Fernando Jiménez'],
    ];

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'admin',
            'specialty_id' => null,
            'is_active' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function asAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Jose Manzanares',
            'email' => 'jose.manzanares@clinica.com',
            'role' => 'admin',
            'specialty_id' => null,
        ]);
    }

    public function asDoctor(): static
    {
        $doctor = self::$doctorNames[self::$doctorIndex % count(self::$doctorNames)];
        self::$doctorIndex++;

        return $this->state(fn (array $attributes) => [
            'name' => $doctor['name'],
            'email' => strtolower(str_replace(' ', '.', $doctor['name']) . '@clinica.com'),
            'role' => 'doctor',
            'specialty_id' => Specialty::inRandomOrder()->first()?->id,
        ]);
    }

    public function asAssistant(): static
    {
        $assistant = self::$assistantNames[self::$assistantIndex % count(self::$assistantNames)];
        self::$assistantIndex++;

        return $this->state(fn (array $attributes) => [
            'name' => $assistant['name'],
            'email' => strtolower(str_replace(' ', '.', $assistant['name']) . '@clinica.com'),
            'role' => 'assistant',
            'specialty_id' => null,
        ]);
    }
}
