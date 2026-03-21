<?php

namespace Database\Factories;

use App\Models\DoctorSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DoctorSchedule>
 */
class DoctorScheduleFactory extends Factory
{
    protected $model = DoctorSchedule::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => '07:00:00',
            'end_time' => '17:00:00',
            'is_active' => true,
        ];
    }

    public function for_doctor(User $doctor): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $doctor->id,
        ]);
    }
}
