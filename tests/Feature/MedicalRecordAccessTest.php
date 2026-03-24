<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\MedicalRecord;
use App\Filament\Resources\MedicalRecords\MedicalRecordResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class MedicalRecordAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_doctor_puede_ver_expediente_clinico()
    {
        $doctor = User::factory()->create(['role' => 'doctor']);
        $record = MedicalRecord::factory()->create();
        Auth::login($doctor);
        $this->assertTrue(MedicalRecordResource::canAccess($record));
    }

    public function test_asistente_no_puede_editar_expediente_clinico()
    {
        $assistant = User::factory()->create(['role' => 'assistant']);
        $record = MedicalRecord::factory()->create();
        Auth::login($assistant);
        $this->assertFalse(MedicalRecordResource::canEdit($record));
    }

    public function test_admin_puede_editar_expediente_clinico()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $record = MedicalRecord::factory()->create();
        Auth::login($admin);
        $this->assertTrue(MedicalRecordResource::canEdit($record));
    }
}
