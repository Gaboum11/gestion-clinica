<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;

class AppointmentsChart extends ChartWidget
{
    protected ?string $heading = 'Citas Ultimos 7 Dias';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $startDate = now()->startOfDay()->subDays(6);
        $endDate = now()->endOfDay();

        $appointments = Appointment::query()
            ->whereBetween('start_datetime', [$startDate, $endDate])
            ->get(['start_datetime']);

        $countsByDate = $appointments
            ->groupBy(fn (Appointment $appointment) => $appointment->start_datetime->format('Y-m-d'))
            ->map(fn ($group) => $group->count());

        $labels = [];
        $data = [];

        for ($day = 6; $day >= 0; $day--) {
            $date = now()->subDays($day);
            $dateKey = $date->format('Y-m-d');

            $labels[] = $date->format('d/m');
            $data[] = $countsByDate[$dateKey] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Citas',
                    'data' => $data,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.2)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
