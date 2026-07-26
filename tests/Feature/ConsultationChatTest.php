<?php

use App\Enums\ConsultationStatus;
use App\Jobs\GenerateConsultationReport;
use App\Livewire\ConsultationChat;
use App\Models\Consultation;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;

function fakeTurnResponse(string $reply, bool $readyForReport = false): CreateResponse
{
    return CreateResponse::fake([
        'model' => 'gpt-4.1',
        'choices' => [[
            'message' => [
                'content' => json_encode([
                    'reply' => $reply,
                    'ready_for_report' => $readyForReport,
                ]),
            ],
        ]],
    ]);
}

function startConsultationThroughChat(string $name = 'Maria Silva', string $email = 'maria@example.com', string $phone = '11999999999')
{
    return Livewire::test(ConsultationChat::class)
        ->assertSet('stage', 'name')
        ->set('input', $name)
        ->call('submitInput')
        ->assertSet('stage', 'email')
        ->set('input', $email)
        ->call('submitInput')
        ->assertSet('stage', 'phone')
        ->set('input', $phone)
        ->call('submitInput')
        ->assertSet('stage', 'project_type');
}

test('a visitor can provide name, email and phone directly in the chat before the fixed questions', function () {
    Queue::fake();

    OpenAI::fake([
        fakeTurnResponse('Quem realiza os agendamentos hoje?'),
    ]);

    $component = startConsultationThroughChat()
        ->call('selectProjectType', 'Um sistema para minha empresa')
        ->assertSet('stage', 'current_process')
        ->set('input', 'Hoje recebemos pedidos pelo WhatsApp e anotamos em uma planilha.')
        ->call('submitInput')
        ->assertSet('stage', 'main_difficulty')
        ->set('input', 'Perdemos muito tempo e esquecemos pedidos.')
        ->call('submitInput')
        ->assertSet('stage', 'ai')
        ->assertHasNoErrors();

    $consultation = Consultation::sole();

    expect($consultation->name)->toBe('Maria Silva')
        ->and($consultation->email)->toBe('maria@example.com')
        ->and($consultation->phone)->toBe('11999999999')
        ->and($consultation->initial_answers['project_type'])->toBe('Um sistema para minha empresa')
        ->and($consultation->messages)->toHaveCount(1)
        ->and($consultation->messages[0]['role'])->toBe('assistant')
        ->and($consultation->messages[0]['content'])->toBe('Quem realiza os agendamentos hoje?')
        ->and($consultation->status)->toBe(ConsultationStatus::InProgress);

    $component->assertSee('Maria Silva')
        ->assertSee('Quem realiza os agendamentos hoje?');
});

test('the "outro" project type option asks for free text before continuing', function () {
    startConsultationThroughChat()
        ->call('selectProjectType', 'Outro')
        ->assertSet('stage', 'project_type_other')
        ->set('input', 'Uma plataforma de agendamento para pet shops')
        ->call('submitInput')
        ->assertSet('stage', 'current_process');
});

test('an invalid email keeps the visitor on the email stage', function () {
    Livewire::test(ConsultationChat::class)
        ->set('input', 'Maria Silva')
        ->call('submitInput')
        ->set('input', 'not-an-email')
        ->call('submitInput')
        ->assertSet('stage', 'email')
        ->assertHasErrors('input');
});

test('sending a message that the ai marks as ready dispatches the report job', function () {
    Queue::fake();

    OpenAI::fake([
        fakeTurnResponse('Quem realiza os agendamentos hoje?'),
        fakeTurnResponse('Perfeito, já tenho tudo que preciso!', readyForReport: true),
    ]);

    startConsultationThroughChat()
        ->call('selectProjectType', 'Um sistema para minha empresa')
        ->set('input', 'Hoje recebemos pedidos pelo WhatsApp.')
        ->call('submitInput')
        ->set('input', 'Perdemos pedidos.')
        ->call('submitInput')
        ->set('input', 'A recepção agenda tudo.')
        ->call('submitInput')
        ->assertSet('step', 'generating');

    $consultation = Consultation::sole();

    expect($consultation->status)->toBe(ConsultationStatus::GeneratingReport)
        ->and($consultation->questions_asked)->toBe(1)
        ->and($consultation->messages)->toHaveCount(3);

    Queue::assertPushed(GenerateConsultationReport::class, fn (GenerateConsultationReport $job): bool => $job->consultation->is($consultation));
});

test('the interview is forced to finish once the maximum number of questions is reached', function () {
    Queue::fake();

    $responses = [fakeTurnResponse('Primeira pergunta adaptativa')];

    for ($i = 0; $i < Consultation::MAX_FOLLOWUP_QUESTIONS; $i++) {
        $responses[] = fakeTurnResponse("Pergunta {$i}", readyForReport: false);
    }

    OpenAI::fake($responses);

    $component = startConsultationThroughChat()
        ->call('selectProjectType', 'Um sistema para minha empresa')
        ->set('input', 'Hoje recebemos pedidos pelo WhatsApp.')
        ->call('submitInput')
        ->set('input', 'Perdemos pedidos.')
        ->call('submitInput');

    for ($i = 0; $i < Consultation::MAX_FOLLOWUP_QUESTIONS; $i++) {
        $component->set('input', "Resposta {$i}")->call('submitInput');
    }

    $component->assertSet('step', 'generating');

    $consultation = Consultation::sole();

    expect($consultation->status)->toBe(ConsultationStatus::GeneratingReport)
        ->and($consultation->questions_asked)->toBe(Consultation::MAX_FOLLOWUP_QUESTIONS);
});

test('starting new consultations is rate limited per ip address', function () {
    for ($i = 0; $i < 5; $i++) {
        startConsultationThroughChat("Visitante {$i}", "visitante{$i}@example.com");
    }

    Livewire::test(ConsultationChat::class)
        ->set('input', 'Visitante extra')
        ->call('submitInput')
        ->set('input', 'extra@example.com')
        ->call('submitInput')
        ->set('input', '11999999999')
        ->call('submitInput')
        ->assertSet('stage', 'phone')
        ->assertHasErrors('input');

    expect(Consultation::count())->toBe(5);
});

test('the poll transitions to the report step once the job completes', function () {
    $consultation = Consultation::factory()->withInitialAnswers()->generatingReport()->create();

    Livewire::test(ConsultationChat::class)
        ->set('consultation', $consultation)
        ->set('step', 'generating')
        ->call('pollReportStatus')
        ->assertSet('step', 'generating');

    $consultation->update(['status' => ConsultationStatus::Completed, 'report' => Consultation::factory()->completed()->make()->report]);

    Livewire::test(ConsultationChat::class)
        ->set('consultation', $consultation)
        ->set('step', 'generating')
        ->call('pollReportStatus')
        ->assertSet('step', 'report')
        ->assertSee($consultation->report['delivery_stages'][0]['name'])
        ->assertDontSee('Equipe');
});

test('the poll transitions to the failed step if report generation fails', function () {
    $consultation = Consultation::factory()->withInitialAnswers()->generatingReport()->create();
    $consultation->update(['status' => ConsultationStatus::Failed, 'ai_error' => 'Falha na IA']);

    Livewire::test(ConsultationChat::class)
        ->set('consultation', $consultation)
        ->set('step', 'generating')
        ->call('pollReportStatus')
        ->assertSet('step', 'failed');
});
