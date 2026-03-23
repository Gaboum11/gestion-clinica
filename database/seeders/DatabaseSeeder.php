<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // First, seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);

        $specialties = [
            ['name' => 'Cardiología', 'description' => 'Especialidad en problemas del corazón y sistema cardiovascular'],
            ['name' => 'Pediatría', 'description' => 'Especialidad en salud de niños y adolescentes'],
            ['name' => 'Dermatología', 'description' => 'Especialidad en enfermedades de la piel'],
            ['name' => 'Neumología', 'description' => 'Especialidad en enfermedades del sistema respiratorio'],
            ['name' => 'Neurología', 'description' => 'Especialidad en trastornos del sistema nervioso'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::firstOrCreate($specialty);
        }

        $admin = User::factory()->asAdmin()->create();
        $admin->syncRoles(['Admin']);

        $doctorNames = [
            'Alejandra Arriola',
            'Alisson Quijano',
            'Melisa Rivas',
            'Karla Contreras',
            'Gabriel Martínez',
        ];

        $doctors = [];
        $specialtyIds = Specialty::pluck('id')->toArray();

        foreach ($doctorNames as $index => $name) {
            $doctor = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name) . '@clinica.com'),
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'doctor',
                'specialty_id' => $specialtyIds[$index % count($specialtyIds)],
                'is_active' => true,
            ]);
            $doctor->syncRoles(['Medico']);
            $doctors[] = $doctor;
        }

        $doctors = collect($doctors);

        $assistantNames = [
            'Patricia Rodrígez',
            'Mario Fernández',
            'Erick Gutiérrez',
        ];

        $assistants = [];
        foreach ($assistantNames as $name) {
            $assistant = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name) . '@clinica.com'),
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
                'role' => 'assistant',
                'specialty_id' => null,
                'is_active' => true,
            ]);
            $assistant->syncRoles(['Asistente']);
            $assistants[] = $assistant;
        }

        $assistants = collect($assistants);

        $patients = Patient::factory(25)->create();

        //expedientes
        $conditions = ['Diabetes', 'Hipertensión', 'Asma', 'Ninguna'];
        $medications = ['Metformina', 'Lisinopril', 'Ninguno'];

        foreach ($patients as $patient) {
            MedicalRecord::create([
                'patient_id' => $patient->id,
                'condition' => fake()->randomElement($conditions),
                'medications' => fake()->randomElement($medications),
                'medical_history' => fake()->paragraph(2),
                'last_visit_date' => fake()->dateTimeBetween('-6 months', 'now'),
                'notes' => fake()->sentence(),
            ]);
        }

        $doctors->each(function ($doctor) {
            DoctorSchedule::factory(5)->for_doctor($doctor)->create();
        });

        //citas con datetime unico
        $appointmentCount = 0;
        $doctors->each(function ($doctor) use (&$appointmentCount) {
            for ($i = 0; $i < 6; $i++) {
                $day = ($appointmentCount % 20) + 1; // 1-20 días en el futuro
                $hour = 7 + ($appointmentCount % 9); // 7am-3pm
                
                Appointment::create([
                    'patient_id' => Patient::inRandomOrder()->first()->id,
                    'doctor_id' => $doctor->id,
                    'start_datetime' => now()->addDays($day)->setHour($hour)->setMinutes(0),
                    'end_datetime' => now()->addDays($day)->setHour($hour + 1)->setMinutes(0),
                    'status' => fake()->randomElement(['scheduled', 'completed', 'cancelled']),
                    'reason' => fake()->sentence(),
                    'notes' => fake()->paragraph(),
                ]);
                $appointmentCount++;
            }
        });
    }
}
