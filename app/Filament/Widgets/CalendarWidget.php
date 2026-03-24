<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class CalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 100;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return $user && 
               $user->is_active && 
               ($user->isAdmin() || $user->isAssistant() || $user->isDoctor());
    }

    public function config(): array
    {
        return [
            'eventClick' => false,
            'selectable' => false,
            'editable'   => false,
        ];
    }

    public function fetchEvents(array $fetchInfo): array
    {
        /** @var User|null $user */
        $user = Auth::user();
        
        if (!$user) return [];

        return Appointment::query()
            ->with(['doctor'])
            ->where('start_datetime', '>=', $fetchInfo['start'])
            ->where('end_datetime', '<=', $fetchInfo['end'])
            ->when($user->isDoctor(), fn($query) => $query->where('doctor_id', $user->id))
            ->get()
            ->map(fn ($appointment) => [
                'id' => $appointment->id,
                'title' => $user->isDoctor() 
                    ? 'Mi Disponibilidad' 
                    : ($appointment->doctor?->name ?? 'Sin Doctor'),
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