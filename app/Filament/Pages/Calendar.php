<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Filament\Widgets\CalendarWidget;

class Calendar extends Page
{
    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Calendario';

    protected string $view = 'filament.pages.calendar';

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        return $user && $user->is_active && ($user->isAdmin() || $user->isAssistant() || $user->isDoctor());
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = Auth::user();
        return $user && ! $user->isDoctor();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            CalendarWidget::class,
        ];
    }
}
