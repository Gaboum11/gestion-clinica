<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('first_name')
                    ->label('Nombre')
                    ->maxLength(100)
                    ->required(),
                TextInput::make('last_name')
                    ->label('Apellido')
                    ->maxLength(100)
                    ->required(),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),
                TextInput::make('phone')
                    ->label('Telefono')
                    ->tel(),
                DatePicker::make('date_of_birth')
                    ->label('Fecha de nacimiento')
                    ->maxDate(now())
                    ->required(),
                Select::make('gender')
                    ->label('Genero')
                    ->options([
                        'M' => 'Masculino',
                        'F' => 'Femenino',
                        'O' => 'Otro',
                    ]),
                Textarea::make('address')
                    ->label('Direccion')
                    ->columnSpanFull(),
                TextInput::make('emergency_contact_name')
                    ->label('Contacto de emergencia'),
                TextInput::make('emergency_contact_phone')
                    ->label('Telefono de emergencia')
                    ->tel(),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true)
                    ->required(),
            ]);
    }
}
