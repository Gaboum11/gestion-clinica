<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active
            && ($user->isAdmin() || $user->isDoctor() || $user->isAssistant());
    }

    public function view(User $user, Appointment $appointment): bool
    {
        if ($user->isDoctor()) {
            return $appointment->doctor_id === $user->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->is_active
            && ($user->isAdmin() || $user->isAssistant());
    }

    public function update(User $user, Appointment $appointment): bool
    {
        if ($user->isDoctor()) {
            return $appointment->doctor_id === $user->id;
        }

        return true;
    }

    public function delete(User $user, Appointment $appointment): bool
    {
        return ! $user->isDoctor();
    }
}
