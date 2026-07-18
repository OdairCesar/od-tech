<?php

namespace App\Filament\Resources\Cities\Schemas;

use App\Enums\PageStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Usado na URL, ex: /cidades/{slug} e nas landing pages {servico}-em-{slug}.'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('state')
                    ->required(),
                TextInput::make('uf')
                    ->required()
                    ->length(2),
                TextInput::make('region')
                    ->required(),
                TextInput::make('population')
                    ->numeric()
                    ->minValue(0),
                TextInput::make('gdp')
                    ->numeric()
                    ->prefix('R$'),
                TextInput::make('latitude')
                    ->numeric(),
                TextInput::make('longitude')
                    ->numeric(),
                Textarea::make('intro')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('business_text')
                    ->required()
                    ->columnSpanFull(),
                Select::make('status')
                    ->options(PageStatus::class)
                    ->default(PageStatus::Draft)
                    ->required(),
            ]);
    }
}
