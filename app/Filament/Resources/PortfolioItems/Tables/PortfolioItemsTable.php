<?php

namespace App\Filament\Resources\PortfolioItems\Tables;

use App\Enums\PageStatus;
use App\Filament\Support\Actions\BulkPublishStatusActions;
use App\Filament\Support\Actions\GenerateImageAction;
use App\Filament\Support\Actions\TogglePublishStatusAction;
use App\Filament\Support\Actions\ViewOnLandingAction;
use App\Models\PortfolioItem;
use App\Services\Portfolio\PortfolioCoverImageGenerator;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PortfolioItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),
                TextColumn::make('service.title')
                    ->label('Serviço')
                    ->searchable(),
                ImageColumn::make('cover_image')
                    ->label('Imagem')
                    ->disk('cloudinary')
                    ->checkFileExistence(false),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('service_id')
                    ->label('Serviço')
                    ->relationship('service', 'title')
                    ->searchable(),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(PageStatus::class),
            ])
            ->recordActions([
                ViewOnLandingAction::make(
                    url: fn (PortfolioItem $record): string => route('portfolio.show', $record),
                    visible: fn (PortfolioItem $record): bool => $record->status === PageStatus::Published,
                    label: 'Ver site',
                ),
                GenerateImageAction::make('cover_image', fn (PortfolioItem $record): string => app(PortfolioCoverImageGenerator::class)->generate(
                    title: $record->title,
                    excerpt: $record->excerpt,
                    serviceTitle: $record->service?->title,
                )),
                TogglePublishStatusAction::make(PageStatus::class),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...BulkPublishStatusActions::make(PageStatus::class),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
