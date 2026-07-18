<?php

namespace App\Filament\Resources\LandingPages\Tables;

use App\Enums\PageStatus;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class LandingPagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.name')
                    ->label('Serviço')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('city.name')
                    ->label('Cidade')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('meta_title')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('custom_h1')
                    ->label('H1 customizado')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('service_id')
                    ->label('Serviço')
                    ->relationship('service', 'name')
                    ->searchable(),
                SelectFilter::make('city_id')
                    ->label('Cidade')
                    ->relationship('city', 'name')
                    ->searchable(),
                SelectFilter::make('status')
                    ->options(PageStatus::class),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('Ver no site')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->visible(fn (): bool => Route::has('landing.show'))
                    ->url(fn ($record) => route('landing.show', $record))
                    ->openUrlInNewTab(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
