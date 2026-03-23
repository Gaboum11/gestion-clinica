<?php

namespace App\Filament\Resources\Patients;

use App\Filament\Resources\Patients\Pages\CreatePatient;
use App\Filament\Resources\Patients\Pages\EditPatient;
use App\Filament\Resources\Patients\Pages\ListPatients;
use App\Filament\Resources\Patients\Pages\ViewPatient;
use App\Filament\Resources\Patients\Schemas\PatientForm;
use App\Filament\Resources\Patients\Tables\PatientsTable;
use App\Models\Patient;
use App\Models\User;
use BackedEnum;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Pacientes';

    protected static string|UnitEnum|null $navigationGroup = 'Operaciones';

    public static function form(Schema $schema): Schema
    {
        return PatientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PatientsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Datos del paciente')
                    ->schema([
                        TextEntry::make('full_name')
                            ->label('Nombre'),
                        TextEntry::make('email')
                            ->label('Correo'),
                        TextEntry::make('phone')
                            ->label('Telefono')
                            ->placeholder('Sin registrar'),
                        TextEntry::make('date_of_birth')
                            ->label('Fecha de nacimiento')
                            ->date(),
                        TextEntry::make('gender')
                            ->label('Genero')
                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                'M' => 'Masculino',
                                'F' => 'Femenino',
                                'O' => 'Otro',
                                default => 'No definido',
                            }),
                        IconEntry::make('is_active')
                            ->label('Activo')
                            ->boolean(),
                    ])
                    ->columns(2),
                Section::make('Expediente clinico')
                    ->schema([
                        TextEntry::make('medicalRecord.condition')
                            ->label('Padecimiento')
                            ->placeholder('Sin registrar'),
                        TextEntry::make('medicalRecord.medications')
                            ->label('Medicacion')
                            ->placeholder('Sin registrar'),
                        TextEntry::make('medicalRecord.last_visit_date')
                            ->label('Ultima visita')
                            ->dateTime()
                            ->placeholder('Sin registrar'),
                        TextEntry::make('medicalRecord.medical_history')
                            ->label('Historial medico')
                            ->placeholder('Sin registrar')
                            ->columnSpanFull(),
                        TextEntry::make('medicalRecord.notes')
                            ->label('Notas')
                            ->placeholder('Sin registrar')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPatients::route('/'),
            'create' => CreatePatient::route('/create'),
            'view' => ViewPatient::route('/{record}'),
            'edit' => EditPatient::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return (bool) $user?->is_active && ($user->isAdmin() || $user->isDoctor() || $user->isAssistant());
    }

    public static function canCreate(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return (bool) $user?->is_active && ($user->isAdmin() || $user->isAssistant());
    }

    public static function canEdit(Model $record): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return (bool) $user?->is_active && ($user->isAdmin() || $user->isAssistant());
    }

    public static function canDelete(Model $record): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return (bool) $user?->is_active && $user->isAdmin();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('medicalRecord');
    }
}
