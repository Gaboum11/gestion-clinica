<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Patient;
use App\Filament\Resources\Patients\PatientResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PatientResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_todos_los_roles_pueden_ver_pacientes()
    {
        foreach (['doctor', 'assistant', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            Auth::login($user);
            $this->assertTrue(PatientResource::canAccess());
        }
    }

    public function test_doctor_no_puede_crear_pacientes()
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        Auth::login($doctor);
        $this->assertFalse(PatientResource::canCreate());
    }

    public function test_asistente_y_admin_pueden_crear_pacientes()
    {
        foreach (['assistant', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            Auth::login($user);
            $this->assertTrue(PatientResource::canCreate());
        }
    }

    public function test_doctor_no_puede_editar_pacientes()
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $patient = Patient::factory()->create();
        Auth::login($doctor);
        $this->assertFalse(PatientResource::canEdit($patient));
    }

    public function test_asistente_y_admin_pueden_editar_pacientes()
    {
        $patient = Patient::factory()->create();
        foreach (['assistant', 'admin'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            Auth::login($user);
            $this->assertTrue(PatientResource::canEdit($patient));
        }
    }

    public function test_solo_admin_puede_eliminar_pacientes()
    {
        $patient = Patient::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        Auth::login($admin);
        $this->assertTrue(PatientResource::canDelete($patient));
    }

    public function test_doctor_y_asistente_no_pueden_eliminar_pacientes()
    {
        $patient = Patient::factory()->create();
        foreach (['doctor', 'assistant'] as $role) {
            $user = User::factory()->create(['role' => $role]);
            Auth::login($user);
            $this->assertFalse(PatientResource::canDelete($patient));
        }
    }
}
