<?php

namespace App\Livewire;

use App\Enums\ConsultationStatus;
use App\Exceptions\AiGenerationException;
use App\Jobs\GenerateConsultationReport;
use App\Models\Consultation;
use App\Services\Consultation\ConsultationChatService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

class ConsultationChat extends Component
{
    /** @var list<string> */
    public array $projectTypeOptions = [
        'Um sistema para minha empresa',
        'Um aplicativo',
        'Um site',
        'Automatizar um processo',
        'Melhorar um sistema existente',
        'Ainda não tenho certeza',
    ];

    public string $step = 'chat';

    public string $stage = 'name';

    public ?Consultation $consultation = null;

    /** @var list<array{role: string, content: string}> */
    public array $chatLog = [];

    public string $input = '';

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $projectType = '';

    public string $currentProcess = '';

    public string $mainDifficulty = '';

    public function mount(): void
    {
        $this->pushAssistant('Olá! Para começar, qual é o seu nome?');
    }

    public function submitInput(ConsultationChatService $chatService): void
    {
        match ($this->stage) {
            'name' => $this->handleName(),
            'email' => $this->handleEmail(),
            'phone' => $this->handlePhone(),
            'project_type_other' => $this->handleProjectTypeOther(),
            'current_process' => $this->handleCurrentProcess(),
            'main_difficulty' => $this->handleMainDifficulty($chatService),
            'ai' => $this->handleAiTurn($chatService),
            default => null,
        };
    }

    public function selectProjectType(string $option): void
    {
        if ($this->stage !== 'project_type') {
            return;
        }

        if ($option === 'Outro') {
            $this->stage = 'project_type_other';
            $this->pushAssistant('Pode contar com suas palavras?');

            return;
        }

        $this->projectType = $option;
        $this->pushUser($option);
        $this->stage = 'current_process';
        $this->pushAssistant('Como você faz isso hoje?');
    }

    public function pollReportStatus(): void
    {
        if (! $this->consultation instanceof Consultation) {
            return;
        }

        $this->consultation->refresh();

        if ($this->consultation->status === ConsultationStatus::Completed) {
            $this->step = 'report';
        } elseif ($this->consultation->status === ConsultationStatus::Failed) {
            $this->step = 'failed';
        }
    }

    private function handleName(): void
    {
        $this->name = $this->validateInput(['required', 'string', 'max:255'], 'nome');
        $this->pushUser($this->name);
        $this->input = '';
        $this->stage = 'email';
        $this->pushAssistant('Qual é o seu e-mail?');
    }

    private function handleEmail(): void
    {
        $this->email = $this->validateInput(['required', 'email', 'max:255'], 'e-mail');
        $this->pushUser($this->email);
        $this->input = '';
        $this->stage = 'phone';
        $this->pushAssistant('Qual é o seu telefone?');
    }

    private function handlePhone(): void
    {
        $phone = $this->validateInput(['required', 'string', 'max:30'], 'telefone');

        $rateLimitKey = 'consultation-start:'.request()->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $this->addError('input', 'Você atingiu o limite de consultas por hora. Tente novamente mais tarde.');

            return;
        }

        RateLimiter::hit($rateLimitKey, 3600);

        $this->phone = $phone;
        $this->pushUser($this->phone);
        $this->input = '';

        $this->consultation = Consultation::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'messages' => [],
            'status' => ConsultationStatus::InProgress,
            'source_url' => request()->headers->get('referer'),
        ]);

        $this->stage = 'project_type';
        $this->pushAssistant('O que você gostaria de criar?');
    }

    private function handleProjectTypeOther(): void
    {
        $this->projectType = $this->validateInput(['required', 'string', 'max:255'], 'resposta');
        $this->pushUser($this->projectType);
        $this->input = '';
        $this->stage = 'current_process';
        $this->pushAssistant('Como você faz isso hoje?');
    }

    private function handleCurrentProcess(): void
    {
        $this->currentProcess = $this->validateInput(['required', 'string', 'max:2000'], 'resposta');
        $this->pushUser($this->currentProcess);
        $this->input = '';
        $this->stage = 'main_difficulty';
        $this->pushAssistant('Qual é a maior dificuldade atualmente?');
    }

    private function handleMainDifficulty(ConsultationChatService $chatService): void
    {
        $this->mainDifficulty = $this->validateInput(['required', 'string', 'max:2000'], 'resposta');
        $this->pushUser($this->mainDifficulty);
        $this->input = '';

        if (! $this->consultation instanceof Consultation) {
            return;
        }

        $this->consultation->update([
            'initial_answers' => [
                'project_type' => $this->projectType,
                'current_process' => $this->currentProcess,
                'main_difficulty' => $this->mainDifficulty,
            ],
        ]);

        try {
            $chatService->startInterview($this->consultation);
            $this->stage = 'ai';
        } catch (AiGenerationException) {
            $this->pushAssistant('Não foi possível iniciar a conversa agora. Tente novamente em instantes.');
        }
    }

    private function handleAiTurn(ConsultationChatService $chatService): void
    {
        if (! $this->consultation instanceof Consultation || $this->consultation->status !== ConsultationStatus::InProgress) {
            return;
        }

        $message = $this->validateInput(['required', 'string', 'max:2000'], 'resposta');
        $this->input = '';

        try {
            $chatService->sendTurn($this->consultation, $message);

            if ($this->consultation->status === ConsultationStatus::GeneratingReport) {
                GenerateConsultationReport::dispatch($this->consultation);
                $this->step = 'generating';
            }
        } catch (AiGenerationException) {
            $this->addError('input', 'Não foi possível processar sua resposta agora. Tente novamente.');
        }
    }

    /**
     * @param  list<string>  $rules
     */
    private function validateInput(array $rules, string $attribute): string
    {
        $this->validate(['input' => $rules], attributes: ['input' => $attribute]);

        return trim($this->input);
    }

    private function pushAssistant(string $content): void
    {
        $this->chatLog[] = ['role' => 'assistant', 'content' => $content];
    }

    private function pushUser(string $content): void
    {
        $this->chatLog[] = ['role' => 'user', 'content' => $content];
    }

    public function render(): View
    {
        return view('livewire.consultation-chat');
    }
}
