<?php

namespace App\Filament\Resources\LandingPages\Schemas;

use App\Enums\PageStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LandingPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->required(),
                Select::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Gerado automaticamente como {servico}-em-{cidade}; só altere se souber o que está fazendo.'),
                TextInput::make('meta_title')
                    ->helperText('Deixe em branco para usar o título gerado automaticamente.'),
                TextInput::make('meta_description')
                    ->helperText('Deixe em branco para usar a descrição gerada automaticamente.'),
                TextInput::make('canonical'),
                TextInput::make('robots')
                    ->required()
                    ->default('index,follow'),
                TextInput::make('custom_h1')
                    ->helperText('Sobrescreve o H1 composto automaticamente para esta página específica.'),
                TextInput::make('custom_subtitle'),
                Textarea::make('custom_intro')
                    ->columnSpanFull(),
                TextInput::make('custom_cta'),
                Select::make('status')
                    ->options(PageStatus::class)
                    ->default(PageStatus::Draft)
                    ->required(),
            ]);
    }
}
