<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

class MedicalRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->is_active;
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return ! $user->isAssistant();
    }
}
