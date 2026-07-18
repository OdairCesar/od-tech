<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->disabled(),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->disabled(),
                TextInput::make('company')
                    ->label('Empresa')
                    ->disabled(),
                Textarea::make('message')
                    ->label('Mensagem')
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('source_url')
                    ->label('Enviado a partir de')
                    ->url()
                    ->disabled(),
                DateTimePicker::make('read_at')
                    ->label('Lido em'),
            ]);
    }
}
