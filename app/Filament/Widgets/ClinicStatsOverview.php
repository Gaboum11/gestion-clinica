<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClinicStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPatients = Patient::count();
        $totalDoctors = User::query()->where('role', 'doctor')->count();
        $appointmentsToday = Appointment::query()
            ->whereDate('start_datetime', today())
            ->count();
        $pendingAppointments = Appointment::query()
            ->where('status', 'scheduled')
            ->count();

        return [
            Stat::make('Pacientes', (string) $totalPatients)
                ->description('Total de pacientes registrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Doctores', (string) $totalDoctors)
                ->description('Profesionales activos en el sistema')
                ->descriptionIcon('heroicon-m-user-circle')
                ->color('success'),

            Stat::make('Citas de Hoy', (string) $appointmentsToday)
                ->description('Agendadas para hoy')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('warning'),

            Stat::make('Citas Pendientes', (string) $pendingAppointments)
                ->description('Estado scheduled')
                ->descriptionIcon('heroicon-m-clock')
                ->color('danger'),
        ];
    }
}
