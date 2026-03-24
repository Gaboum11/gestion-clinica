<?php

namespace Tests\Feature;

use App\Models\User;
use App\Filament\Resources\Users\UserResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_puede_crear_editar_y_desactivar_usuarios()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        Auth::login($admin);
        $this->assertTrue(UserResource::canCreate());
        $this->assertTrue(UserResource::canEdit($user));
        $this->assertTrue(UserResource::canDelete($user));
    }

    public function test_doctor_y_asistente_no_pueden_gestionar_usuarios()
    {
        $user = User::factory()->create();
        foreach (['doctor', 'assistant'] as $role) {
            $noAdmin = User::factory()->create(['role' => $role]);
            Auth::login($noAdmin);
            $this->assertFalse(UserResource::canAccess());
            $this->assertFalse(UserResource::canCreate());
            $this->assertFalse(UserResource::canEdit($user));
            $this->assertFalse(UserResource::canDelete($user));
        }
    }

    public function test_usuario_inactivo_no_puede_acceder_al_panel()
    {
        $inactive = User::factory()->create(['role' => 'doctor', 'is_active' => false]);
        Auth::login($inactive);
        $this->assertFalse(UserResource::canAccessPanel());
    }
}
