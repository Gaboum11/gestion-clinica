<?php

namespace App\Policies;

use App\Models\Appointment;
use App\Models\User;

class AppointmentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
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
        return $user->is_active;
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
