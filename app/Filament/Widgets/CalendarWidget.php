<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 100;
    protected int | string | array $columnSpan = 'full';

    public function fetchEvents(array $fetchInfo): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        return Appointment::query()
            ->with(['patient', 'doctor'])
            ->where('start_datetime', '>=', $fetchInfo['start'])
            ->where('end_datetime', '<=', $fetchInfo['end'])
            ->when($user->isDoctor(), fn($query) => $query->where('doctor_id', $user->id))
            ->get()
            ->map(fn ($appointment) => [
                'id' => $appointment->id,
                'title' => $user->isDoctor() 
                    ? ($appointment->patient?->name ?? 'Paciente')
                    : ($appointment->patient?->name . ' - ' . ($appointment->doctor?->name ?? 'Dr.')),
                'start' => $appointment->start_datetime,
                'end' => $appointment->end_datetime,
                'color' => $appointment->doctor_id === $user->id ? '#3b82f6' : '#10b981',
            ])
            ->toArray();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
