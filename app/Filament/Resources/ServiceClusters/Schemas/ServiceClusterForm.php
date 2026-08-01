<?php

namespace App\Filament\Resources\ServiceClusters\Schemas;

use App\Enums\ServiceClusterStatus;
use App\Filament\Support\Forms\CloudinaryImageUpload;
use App\Models\ServiceCluster;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ServiceClusterForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('service_id')
                    ->label('Serviço')
                    ->relationship('service', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Usado na URL, ex: /servicos/{servico}/{slug}.'),
                TextInput::make('name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('subtitle')
                    ->required(),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                TagsInput::make('benefits')
                    ->required()
                    ->columnSpanFull(),
                Repeater::make('faq')
                    ->schema([
                        TextInput::make('question')->required(),
                        Textarea::make('answer')->required(),
                    ])
                    ->itemLabel(fn (array $state): ?string => is_string($state['question'] ?? null) ? $state['question'] : null)
                    ->collapsible()
                    ->columnSpanFull(),
                TagsInput::make('keywords')
                    ->required()
                    ->columnSpanFull(),
                CloudinaryImageUpload::make('hero_image')
                    ->label('Imagem do hero')
                    ->helperText('Deixe em branco para usar a imagem do serviço pai.'),
                Select::make('status')
                    ->options(ServiceClusterStatus::class)
                    ->default(ServiceClusterStatus::Draft)
                    ->required(),
                Section::make('SEO')
                    ->collapsible()
                    ->components([
                        TextInput::make('meta_title')
                            ->helperText('Deixe em branco para usar o título gerado automaticamente.'),
                        TextInput::make('meta_description')
                            ->helperText('Deixe em branco para usar a descrição gerada automaticamente.'),
                        TextInput::make('canonical'),
                        TextInput::make('robots')
                            ->required()
                            ->default('index,follow'),
                    ]),
                KeyValue::make('ai_brief')
                    ->label('Briefing usado pela IA')
                    ->disabled()
                    ->visible(fn (?ServiceCluster $record): bool => filled($record?->ai_brief))
                    ->columnSpanFull(),
                Textarea::make('ai_error')
                    ->label('Erro da geração')
                    ->disabled()
                    ->visible(fn (?ServiceCluster $record): bool => $record?->status === ServiceClusterStatus::Failed)
                    ->columnSpanFull(),
            ]);
    }
}
