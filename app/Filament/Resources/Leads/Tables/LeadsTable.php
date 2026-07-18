<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source')
                    ->label('Origem')
                    ->badge(),
                TextColumn::make('name')
                    ->label('Nome')
                    ->weight(fn (Lead $record): string => $record->read_at === null ? 'bold' : 'normal')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone'),
                TextColumn::make('message')
                    ->label('Mensagem')
                    ->limit(60)
                    ->wrap(),
                TextColumn::make('read_at')
                    ->label('Lido em')
                    ->dateTime()
                    ->placeholder('Não lido')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Recebido em')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                TernaryFilter::make('read_at')
                    ->label('Status')
                    ->nullable()
                    ->trueLabel('Lidos')
                    ->falseLabel('Não lidos')
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('read_at'),
                        false: fn (Builder $query): Builder => $query->whereNull('read_at'),
                    ),
            ])
            ->recordActions([
                Action::make('markAsRead')
                    ->label('Marcar como lido')
                    ->icon('heroicon-o-check')
                    ->visible(fn (Lead $record): bool => $record->read_at === null)
                    ->action(fn (Lead $record) => $record->update(['read_at' => now()])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
