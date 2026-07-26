<?php

namespace App\Filament\Resources\Consultations\Tables;

use App\Enums\ConsultationStatus;
use App\Models\Consultation;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ConsultationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nome')
                    ->weight(fn (Consultation $record): string => $record->read_at === null ? 'bold' : 'normal')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('questions_asked')
                    ->label('Perguntas')
                    ->alignCenter(),
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
                SelectFilter::make('status')
                    ->label('Status')
                    ->options(fn (): array => collect(ConsultationStatus::cases())
                        ->mapWithKeys(fn (ConsultationStatus $status): array => [$status->value => $status->getLabel()])
                        ->all()),
                TernaryFilter::make('read_at')
                    ->label('Leitura')
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
                    ->visible(fn (Consultation $record): bool => $record->read_at === null)
                    ->action(fn (Consultation $record) => $record->update(['read_at' => now()])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
