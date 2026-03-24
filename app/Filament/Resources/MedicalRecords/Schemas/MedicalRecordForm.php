<?php

namespace App\Filament\Resources\MedicalRecords\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class MedicalRecordForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('patient_id')
                    ->label('Paciente')
                    ->relationship('patient', 'first_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name)
                    ->disabledOn('edit')
                    ->required(),
                Textarea::make('condition')
                    ->label('Padecimiento')
                    ->columnSpanFull(),
                Textarea::make('medications')
                    ->label('Medicacion')
                    ->columnSpanFull(),
                Textarea::make('medical_history')
                    ->label('Historial medico')
                    ->columnSpanFull(),
                DateTimePicker::make('last_visit_date')
                    ->label('Ultima visita')
                    ->seconds(false)
                    ->native(false),
                Textarea::make('notes')
                    ->label('Notas')
                    ->columnSpanFull(),
            ]);
    }
}
