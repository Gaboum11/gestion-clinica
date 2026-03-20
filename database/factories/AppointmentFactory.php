<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $startDateTime = $this->faker->dateTimeBetween('+1 day', '+30 days');
        $startDateTime->setTime(
            $this->faker->numberBetween(7, 16),
            $this->faker->randomElement([0, 30]),
            0
        );

        $endDateTime = clone $startDateTime;
        $endDateTime->modify('+1 hour');

        return [
            'patient_id' => Patient::inRandomOrder()->first()?->id ?? Patient::factory(),
            'doctor_id' => User::where('role', 'doctor')->first()?->id ?? User::factory()->asDoctor()->create()->id,
            'start_datetime' => $startDateTime,
            'end_datetime' => $endDateTime,
            'status' => $this->faker->randomElement(['scheduled', 'completed', 'cancelled']),
            'reason' => $this->faker->sentence(),
            'notes' => $this->faker->paragraph(),
        ];
    }
}
