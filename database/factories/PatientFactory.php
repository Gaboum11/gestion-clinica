<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    private static array $firstNames = [
        'Juan', 'María', 'Carlos', 'Ana', 'José', 'Rosa', 'Manuel', 'Laura', 'Francisco', 'Carmen',
        'Pedro', 'Juana', 'Miguel', 'Beatriz', 'Diego', 'Mercedes', 'Andrés', 'Patricia', 'Ricardo', 'Isabel',
        'Fernando', 'Alejandra', 'Jorge', 'Alicia', 'Luis', 'Margarita', 'Gustavo', 'Catalina', 'Raúl', 'Claudia',
    ];

    private static array $lastNames = [
        'García', 'Rodríguez', 'Martínez', 'Hernández', 'López', 'González', 'Pérez', 'Sánchez', 'Ramírez', 'Torres',
        'Rivera', 'Cruz', 'Flores', 'Romero', 'Moreno', 'Gutiérrez', 'Ortiz', 'Jiménez', 'Vargas', 'Castillo',
        'Herrera', 'Campos', 'Medina', 'Vega', 'Contreras', 'Fuentes', 'Reyes', 'Rojas', 'Ríos', 'Aguilar',
    ];

    public function definition(): array
    {
        $firstName = $this->faker->randomElement(self::$firstNames);
        $lastName = $this->faker->randomElement(self::$lastNames);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->numerify('##########'),
            'date_of_birth' => $this->faker->dateTimeBetween('-80 years', '-18 years'),
            'gender' => $this->faker->randomElement(['M', 'F', 'O']),
            'address' => $this->faker->address(),
            'emergency_contact_name' => $this->faker->name(),
            'emergency_contact_phone' => $this->faker->numerify('##########'),
            'is_active' => true,
        ];
    }
}
