<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\User;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => ['required', 'exists:patients,id'],
            'doctor_id' => ['required', 'exists:users,id'],
            'start_datetime' => ['required', 'date'],
            'end_datetime' => ['required', 'date', 'after:start_datetime'],
            'reason' => ['nullable', 'string'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {

            // Si ya fallaron validaciones básicas, no continuar
            if ($validator->errors()->any()) return;

            $doctorId = $this->doctor_id;
            $start = $this->start_datetime;
            $end = $this->end_datetime;

            $carbonStart = Carbon::parse($start);
            $carbonEnd = Carbon::parse($end);

            // Validar que el usuario sea doctor
            $user = User::find($doctorId);

            if (!$user || !$user->isDoctor()) {
                $validator->errors()->add('doctor_id', 'El usuario seleccionado no es un doctor.');
                return;
            }

            // Evitar citas en el pasado
            if ($carbonStart->isPast()) {
                $validator->errors()->add('start_datetime', 'No puedes agendar citas en el pasado.');
                return;
            }

            // ⏱Validar duración mínima (10 min)
            if ($carbonStart->diffInMinutes($carbonEnd) < 10) {
                $validator->errors()->add('end_datetime', 'La cita debe durar al menos 10 minutos.');
            }

            // Validar el tiempo de citas
            $overlap = Appointment::where('doctor_id', $doctorId)
                ->where(function ($query) use ($start, $end) {
                    $query->where(function ($q) use ($start, $end) {
                        $q->where('start_datetime', '<', $end)
                          ->where('end_datetime', '>', $start);
                    });
                })
                ->exists();

            if ($overlap) {
                $validator->errors()->add('doctor_id', 'El doctor ya tiene una cita en ese horario o existe un traslape.');
            }

            // Validar horario del doctor
            $dayOfWeek = $carbonStart->dayOfWeek;

            $schedule = DoctorSchedule::where('user_id', $doctorId)
                ->where('day_of_week', $dayOfWeek)
                ->where('is_active', true)
                ->first();

            if (!$schedule) {
                $validator->errors()->add('doctor_id', 'El doctor no trabaja el día seleccionado.');
                return;
            }

            $startTime = $carbonStart->format('H:i:s');
            $endTime = $carbonEnd->format('H:i:s');

            if ($startTime < $schedule->start_time || $endTime > $schedule->end_time) {
                $validator->errors()->add(
                    'start_datetime',
                    'La cita debe estar dentro del horario laboral del doctor (' .
                    $schedule->start_time . ' - ' . $schedule->end_time . ').'
                );
            }
        });
    }
}