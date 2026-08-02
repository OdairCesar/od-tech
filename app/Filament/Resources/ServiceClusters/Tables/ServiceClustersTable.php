<?php

namespace App\Filament\Resources\ServiceClusters\Tables;

use App\Enums\ServiceClusterStatus;
use App\Filament\Support\Actions\ViewOnLandingAction;
use App\Models\ServiceCluster;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ServiceClustersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.title')
                    ->label('Serviço')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->placeholder('Gerando com IA...'),
                ImageColumn::make('hero_image')
                    ->disk('cloudinary'),
                TextColumn::make('landing_pages_count')
                    ->label('Cidades')
                    ->counts('landingPages'),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('service_id')
                    ->label('Serviço')
                    ->relationship('service', 'title')
                    ->searchable(),
                SelectFilter::make('status')
                    ->options(ServiceClusterStatus::class),
                TernaryFilter::make('hero_image')
                    ->label('Imagem')
                    ->nullable()
                    ->placeholder('Todos')
                    ->trueLabel('Com imagem')
                    ->falseLabel('Sem imagem'),
            ])
            ->recordActions([
                ViewOnLandingAction::make(
                    url: fn (ServiceCluster $record): string => route('services.clusters.show', [$record->service, $record]),
                    visible: fn (ServiceCluster $record): bool => $record->status === ServiceClusterStatus::Published,
                ),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
