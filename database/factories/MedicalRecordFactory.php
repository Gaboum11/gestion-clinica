<?php

namespace Database\Factories;

use App\Models\MedicalRecord;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MedicalRecord>
 */
class MedicalRecordFactory extends Factory
{
    protected $model = MedicalRecord::class;

    private static array $conditions = [
        'infarto al miocardio',
        'hipertensión arterial',
        'arritmia cardiaca',
        'varicela',
        'asma infantil',
        'fiebre reumática',
        'psoriasis',
        'acné severo',
        'rosácea crónica',
        'asma bronquial',
        'fibrosis pulmonar',
        'sindrome de apnea del sueño',
        'epilepsia',
        'migraña crónica',
        'ninguna',
    ];

    private static array $medications = [
        'metformina',
        'aspirina',
        'salbutamol',
        'penicilina',
        'tretinoina',
        'metotrexato',
        'budesonida',
        'ipratropio',
        'carbamazepina',
        'levodopa',
        'ninguno',
    ];

    public function definition(): array
    {
        return [
            'patient_id' => Patient::inRandomOrder()->first()?->id ?? Patient::factory(),
            'condition' => $this->faker->randomElement(self::$conditions),
            'medications' => $this->faker->randomElement(self::$medications),
            'medical_history' => $this->faker->paragraph(2),
            'last_visit_date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'notes' => $this->faker->sentence(),
        ];
    }
}
