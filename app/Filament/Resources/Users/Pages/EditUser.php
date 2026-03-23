<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var User $user */
        $user = $this->record;

        $user->syncRoles([$this->mapRoleToSpatie($user->role)]);
    }

    private function mapRoleToSpatie(string $role): string
    {
        return match ($role) {
            'admin' => 'Admin',
            'doctor' => 'Medico',
            'assistant' => 'Asistente',
            default => 'Asistente',
        };
    }
}
