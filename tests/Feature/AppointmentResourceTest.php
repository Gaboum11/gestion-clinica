<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Appointment;
use App\Filament\Resources\Appointments\AppointmentResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AppointmentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Factory setup if needed
    }

    public function test_doctor_solo_ve_sus_citas()
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $otherDoctor = User::factory()->create(['role' => 'doctor']);
        $myAppointment = Appointment::factory()->create(['doctor_id' => $doctor->id]);
        $otherAppointment = Appointment::factory()->create(['doctor_id' => $otherDoctor->id]);
        Auth::login($doctor);
        $query = AppointmentResource::getEloquentQuery();
        $ids = $query->pluck('id')->toArray();
        $this->assertContains($myAppointment->id, $ids);
        $this->assertNotContains($otherAppointment->id, $ids);
    }

    public function test_asistente_y_admin_ven_todas_las_citas()
    {
        $assistant = User::factory()->create(['role' => 'assistant']);
        $admin = User::factory()->create(['role' => 'admin']);
        $doctor = User::factory()->create(['role' => 'doctor']);
        $appointment1 = Appointment::factory()->create(['doctor_id' => $doctor->id]);
        $appointment2 = Appointment::factory()->create(['doctor_id' => $doctor->id]);
        foreach ([$assistant, $admin] as $user) {
            Auth::login($user);
            $query = AppointmentResource::getEloquentQuery();
            $ids = $query->pluck('id')->toArray();
            $this->assertContains($appointment1->id, $ids);
            $this->assertContains($appointment2->id, $ids);
        }
    }

    public function test_doctor_no_puede_crear_citas()
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        Auth::login($doctor);
        $this->assertFalse(AppointmentResource::canCreate());
    }

    public function test_asistente_y_admin_pueden_crear_citas()
    {
        $assistant = User::factory()->create(['role' => 'assistant']);
        $admin = User::factory()->create(['role' => 'admin']);
        foreach ([$assistant, $admin] as $user) {
            Auth::login($user);
            $this->assertTrue(AppointmentResource::canCreate());
        }
    }

    public function test_doctor_solo_puede_editar_sus_citas()
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $otherDoctor = User::factory()->create(['role' => 'doctor']);
        $myAppointment = Appointment::factory()->create(['doctor_id' => $doctor->id]);
        $otherAppointment = Appointment::factory()->create(['doctor_id' => $otherDoctor->id]);
        Auth::login($doctor);
        $this->assertTrue(AppointmentResource::canEdit($myAppointment));
        $this->assertFalse(AppointmentResource::canEdit($otherAppointment));
    }

    public function test_doctor_no_puede_eliminar_citas()
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $appointment = Appointment::factory()->create(['doctor_id' => $doctor->id]);
        Auth::login($doctor);
        $this->assertFalse(AppointmentResource::canDelete($appointment));
    }

    public function test_asistente_y_admin_pueden_eliminar_citas()
    {
        $assistant = User::factory()->create(['role' => 'assistant']);
        $admin = User::factory()->create(['role' => 'admin']);
        $appointment = Appointment::factory()->create();
        foreach ([$assistant, $admin] as $user) {
            Auth::login($user);
            $this->assertTrue(AppointmentResource::canDelete($appointment));
        }
    }
}
