<?php

namespace Database\Factories;

use App\Models\Specialty;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Specialty>
 */
class SpecialtyFactory extends Factory
{
    protected $model = Specialty::class;

    public function definition(): array
    {
        $specialties = [
            ['name' => 'cardiología', 'description' => 'Especialidad en problemas del corazón y sistema cardiovascular'],
            ['name' => 'pediatría', 'description' => 'Especialidad en salud de niños y adolescentes'],
            ['name' => 'dermatología', 'description' => 'Especialidad en enfermedades de la piel'],
            ['name' => 'neumología', 'description' => 'Especialidad en enfermedades del sistema respiratorio'],
            ['name' => 'neurología', 'description' => 'Especialidad en trastornos del sistema nervioso'],
        ];

        return $specialties[array_rand($specialties)];
    }
}
