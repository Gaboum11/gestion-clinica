<?php

namespace App\Filament\Resources\Appointments\Schemas;

use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AppointmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->label('Paciente')
                    ->options(fn () => Patient::query()->orderBy('first_name')->get()->pluck('full_name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('doctor_id')
                    ->label('Medico')
                    ->options(fn () => User::query()
                        ->where('role', 'doctor')
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required(),
                DateTimePicker::make('start_datetime')
                    ->label('Inicio')
                    ->seconds(false)
                    ->minutesStep(10)
                    ->native(false)
                    ->live()
                    ->required(),
                DateTimePicker::make('end_datetime')
                    ->label('Fin')
                    ->seconds(false)
                    ->minutesStep(10)
                    ->native(false)
                    ->live()
                    ->rules([
                        fn (Get $get, ?Appointment $record): Closure => function (string $attribute, $value, Closure $fail) use ($get, $record): void {
                            $doctorId = (int) $get('doctor_id');
                            $startValue = $get('start_datetime');

                            if (! $doctorId || blank($startValue) || blank($value)) {
                                return;
                            }

                            try {
                                $start = Carbon::parse($startValue);
                                $end = Carbon::parse($value);
                            } catch (\Throwable) {
                                $fail('Las fechas de la cita no son validas.');

                                return;
                            }

                            if ($end->lessThanOrEqualTo($start)) {
                                $fail('La fecha de fin debe ser mayor que la fecha de inicio.');

                                return;
                            }

                            if ($start->isPast()) {
                                $fail('No puedes agendar citas en el pasado.');

                                return;
                            }

                            if ($start->diffInMinutes($end) < 10) {
                                $fail('La cita debe durar al menos 10 minutos.');
                            }

                            $doctor = User::find($doctorId);

                            if (! $doctor || ! $doctor->isDoctor() || ! $doctor->is_active) {
                                $fail('El usuario seleccionado no es un medico activo.');

                                return;
                            }

                            $overlapQuery = Appointment::query()
                                ->where('doctor_id', $doctorId)
                                ->where('start_datetime', '<', $end)
                                ->where('end_datetime', '>', $start);

                            if ($record) {
                                $overlapQuery->whereKeyNot($record->getKey());
                            }

                            if ($overlapQuery->exists()) {
                                $fail('El medico ya tiene una cita en ese horario.');

                                return;
                            }

                            $schedule = DoctorSchedule::query()
                                ->where('user_id', $doctorId)
                                ->where('day_of_week', $start->dayOfWeek)
                                ->where('is_active', true)
                                ->first();

                            if (! $schedule) {
                                $fail('El medico no trabaja el dia seleccionado.');

                                return;
                            }

                            $startTime = $start->format('H:i:s');
                            $endTime = $end->format('H:i:s');

                            if ($startTime < $schedule->start_time || $endTime > $schedule->end_time) {
                                $fail("La cita debe estar dentro del horario laboral del medico ({$schedule->start_time} - {$schedule->end_time}).");
                            }
                        },
                    ])
                    ->required(),
                Select::make('status')
                    ->label('Estado')
                    ->options([
                        'scheduled' => 'Programada',
                        'completed' => 'Completada',
                        'cancelled' => 'Cancelada',
                    ])
                    ->default('scheduled')
                    ->required(),
                Textarea::make('reason')
                    ->label('Motivo')
                    ->rows(2),
                Textarea::make('notes')
                    ->label('Notas')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
