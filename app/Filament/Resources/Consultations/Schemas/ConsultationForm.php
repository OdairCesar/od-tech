<?php

namespace App\Filament\Resources\Consultations\Schemas;

use App\Enums\ConsultationStatus;
use App\Models\Consultation;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class ConsultationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->disabled(),
                TextInput::make('email')
                    ->label('E-mail')
                    ->email()
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Telefone')
                    ->tel()
                    ->disabled(),
                TextInput::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (ConsultationStatus|string|null $state): string => $state instanceof ConsultationStatus
                        ? $state->getLabel()
                        : ConsultationStatus::tryFrom((string) $state)?->getLabel() ?? '')
                    ->disabled(),
                TextInput::make('questions_asked')
                    ->label('Perguntas feitas pela IA')
                    ->disabled(),
                KeyValue::make('initial_answers')
                    ->label('Respostas iniciais')
                    ->disabled()
                    ->columnSpanFull(),
                Textarea::make('messages')
                    ->label('Conversa')
                    ->formatStateUsing(fn (?array $state): string => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '')
                    ->disabled()
                    ->rows(12)
                    ->extraInputAttributes(['class' => 'font-mono text-xs'])
                    ->columnSpanFull(),
                Section::make('Relatório gerado')
                    ->description('Análise completa produzida pela IA a partir da entrevista com o cliente.')
                    ->visible(fn (Consultation $record): bool => $record->report !== null)
                    ->columns(2)
                    ->schema([
                        TextEntry::make('report.executive_summary')
                            ->label('Resumo executivo')
                            ->columnSpanFull(),
                        TextEntry::make('report.problem')
                            ->label('Problema identificado')
                            ->columnSpanFull(),
                        TextEntry::make('report.objectives')
                            ->label('Objetivos do projeto')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->columnSpanFull(),

                        Grid::make(3)
                            ->columnSpanFull()
                            ->schema([
                                TextEntry::make('report.features_essential')
                                    ->label('Funcionalidades essenciais')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                                TextEntry::make('report.features_recommended')
                                    ->label('Funcionalidades recomendadas')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                                TextEntry::make('report.features_future')
                                    ->label('Funcionalidades futuras')
                                    ->listWithLineBreaks()
                                    ->bulleted(),
                            ]),

                        TextEntry::make('report.user_profiles')
                            ->label('Perfis de usuários')
                            ->listWithLineBreaks()
                            ->columnSpanFull(),

                        TextEntry::make('report.current_flow')
                            ->label('Fluxo atual'),
                        TextEntry::make('report.desired_flow')
                            ->label('Fluxo desejado'),

                        TextEntry::make('report.integrations')
                            ->label('Integrações sugeridas')
                            ->listWithLineBreaks()
                            ->visible(fn (Consultation $record): bool => filled($record->report['integrations'] ?? null))
                            ->columnSpanFull(),

                        TextEntry::make('report.tech_stack')
                            ->label('Stack técnica sugerida (uso interno)')
                            ->badge()
                            ->color('gray')
                            ->columnSpanFull(),

                        TextEntry::make('report.complexity')
                            ->label('Complexidade')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state)))
                            ->color(fn (string $state): string => match ($state) {
                                'baixa' => 'success',
                                'media' => 'info',
                                'alta' => 'warning',
                                'muito_alta' => 'danger',
                                default => 'gray',
                            }),
                        TextEntry::make('report.mvp')
                            ->label('MVP sugerido'),
                        TextEntry::make('report.complexity_justification')
                            ->label('Justificativa da complexidade')
                            ->columnSpanFull(),

                        TextEntry::make('report.next_phases')
                            ->label('Próximas fases')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->visible(fn (Consultation $record): bool => filled($record->report['next_phases'] ?? null))
                            ->columnSpanFull(),

                        Section::make('Estimativa inicial')
                            ->columnSpanFull()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('report.estimate_timeframe')
                                            ->label('Prazo total')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('report.estimate_investment')
                                            ->label('Investimento total')
                                            ->weight(FontWeight::Bold),
                                    ]),

                                RepeatableEntry::make('report.delivery_stages')
                                    ->label('Etapas de entrega')
                                    ->schema([
                                        TextEntry::make('name')
                                            ->label('Etapa')
                                            ->weight(FontWeight::SemiBold)
                                            ->columnSpanFull(),
                                        TextEntry::make('description')
                                            ->label('Descrição')
                                            ->columnSpanFull(),
                                        TextEntry::make('timeframe')
                                            ->label('Prazo'),
                                        TextEntry::make('investment')
                                            ->label('Investimento'),
                                    ])
                                    ->columns(2)
                                    ->visible(fn (Consultation $record): bool => filled($record->report['delivery_stages'] ?? null)),
                            ]),

                        TextEntry::make('report.open_questions')
                            ->label('Pontos que ainda precisam ser esclarecidos')
                            ->listWithLineBreaks()
                            ->bulleted()
                            ->visible(fn (Consultation $record): bool => filled($record->report['open_questions'] ?? null))
                            ->columnSpanFull(),
                    ]),

                Section::make('Relatório')
                    ->visible(fn (Consultation $record): bool => $record->report === null)
                    ->schema([
                        TextEntry::make('report_empty_notice')
                            ->label('')
                            ->formatStateUsing(fn (Consultation $record): string => match (true) {
                                $record->status === ConsultationStatus::Failed => 'A geração do relatório falhou. Verifique o erro técnico abaixo.',
                                default => 'Nenhum relatório gerado até o momento.',
                            })
                            ->color(fn (Consultation $record): string => $record->status === ConsultationStatus::Failed ? 'danger' : 'gray'),
                    ]),
                TextInput::make('ai_model')
                    ->label('Modelo de IA')
                    ->disabled(),
                Textarea::make('ai_error')
                    ->label('Erro da IA')
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
