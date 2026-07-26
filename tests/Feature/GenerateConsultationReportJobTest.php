<?php

use App\Enums\ConsultationStatus;
use App\Jobs\GenerateConsultationReport;
use App\Mail\NewConsultationCompleted;
use App\Models\Consultation;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

function fakeReportResponse(): CreateResponse
{
    return CreateResponse::fake([
        'model' => 'gpt-4.1',
        'choices' => [[
            'message' => [
                'content' => json_encode([
                    'executive_summary' => 'Resumo executivo de teste.',
                    'problem' => 'Problema identificado de teste.',
                    'objectives' => ['Objetivo 1'],
                    'features_essential' => ['Funcionalidade essencial 1'],
                    'features_recommended' => ['Funcionalidade recomendada 1'],
                    'features_future' => ['Funcionalidade futura 1'],
                    'user_profiles' => ['Administrador', 'Cliente'],
                    'current_flow' => 'Fluxo atual de teste.',
                    'desired_flow' => 'Fluxo desejado de teste.',
                    'integrations' => ['WhatsApp'],
                    'tech_stack' => ['Laravel'],
                    'complexity' => 'media',
                    'complexity_justification' => 'Justificativa de teste.',
                    'mvp' => 'Descrição do MVP de teste.',
                    'next_phases' => ['Fase futura 1'],
                    'estimate_timeframe' => '3 a 5 meses',
                    'estimate_investment' => 'R$ 40.000 a R$ 70.000',
                    'delivery_stages' => [
                        [
                            'name' => 'Configuração inicial e autenticação',
                            'description' => 'Estrutura do projeto, cadastro e login de usuários.',
                            'timeframe' => '2 semanas',
                            'investment' => 'R$ 8.000',
                        ],
                        [
                            'name' => 'Cadastro de produtos e clientes',
                            'description' => 'Telas e regras para gerenciar produtos e clientes.',
                            'timeframe' => '3 semanas',
                            'investment' => 'R$ 12.000',
                        ],
                    ],
                    'open_questions' => ['Pergunta em aberto 1'],
                ]),
            ],
        ]],
    ]);
}

test('generating a report succeeds and notifies the team', function () {
    Mail::fake();
    OpenAI::fake([fakeReportResponse()]);

    $consultation = Consultation::factory()->withInitialAnswers()->generatingReport()->create();

    Bus::dispatchSync(new GenerateConsultationReport($consultation));

    $consultation->refresh();

    expect($consultation->status)->toBe(ConsultationStatus::Completed)
        ->and($consultation->ai_model)->toBe('gpt-4.1')
        ->and($consultation->ai_error)->toBeNull()
        ->and($consultation->report['executive_summary'])->toBe('Resumo executivo de teste.')
        ->and($consultation->report['complexity'])->toBe('media');

    Mail::assertSent(NewConsultationCompleted::class, fn (NewConsultationCompleted $mail): bool => $mail->consultation->is($consultation));
});

test('an invalid ai response marks the consultation as failed', function () {
    OpenAI::fake([
        CreateResponse::fake([
            'choices' => [[
                'message' => [
                    'content' => json_encode(['executive_summary' => 'Só isso, faltando o resto.']),
                ],
            ]],
        ]),
    ]);

    $consultation = Consultation::factory()->withInitialAnswers()->generatingReport()->create();

    Bus::dispatchSync(new GenerateConsultationReport($consultation));

    $consultation->refresh();

    expect($consultation->status)->toBe(ConsultationStatus::Failed)
        ->and($consultation->ai_error)->not->toBeNull();
});

test('the failed hook marks the consultation as failed with the exception message', function () {
    $consultation = Consultation::factory()->withInitialAnswers()->generatingReport()->create();

    (new GenerateConsultationReport($consultation))->failed(new Exception('Falha de conexão com a OpenAI'));

    $consultation->refresh();

    expect($consultation->status)->toBe(ConsultationStatus::Failed)
        ->and($consultation->ai_error)->toBe('Falha de conexão com a OpenAI');
});
