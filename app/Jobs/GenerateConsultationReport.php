<?php

namespace App\Jobs;

use App\Enums\ConsultationStatus;
use App\Exceptions\AiGenerationException;
use App\Mail\NewConsultationCompleted;
use App\Models\Consultation;
use App\Services\Consultation\ConsultationReportParser;
use App\Services\Consultation\ConsultationReportPromptBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;
use Throwable;

class GenerateConsultationReport implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60];

    public int $timeout = 300;

    public function __construct(public readonly Consultation $consultation) {}

    public function handle(
        ConsultationReportPromptBuilder $promptBuilder,
        ConsultationReportParser $parser,
    ): void {
        try {
            $messages = $promptBuilder->build($this->consultation);

            $response = OpenAI::chat()->create([
                'model' => config('services.openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => $messages['system']],
                    ['role' => 'user', 'content' => $messages['user']],
                ],
                'response_format' => $promptBuilder->responseFormat(),
                'temperature' => 0.5,
            ]);

            $report = $parser->parse($response->choices[0]->message->content ?? '');
        } catch (AiGenerationException $exception) {
            $this->markFailed($exception);

            return;
        }

        $this->consultation->update([
            'report' => $report->toArray(),
            'ai_model' => $response->model,
            'status' => ConsultationStatus::Completed,
            'ai_error' => null,
        ]);

        try {
            Mail::to(config('services.lead_notifications.email'))->send(new NewConsultationCompleted($this->consultation));
        } catch (Throwable $exception) {
            Log::error('Falha ao enviar e-mail de notificação de consultoria de IA.', [
                'consultation_id' => $this->consultation->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $this->markFailed($exception);
    }

    private function markFailed(?Throwable $exception): void
    {
        $this->consultation->update([
            'status' => ConsultationStatus::Failed,
            'ai_error' => $exception ? Str::limit($exception->getMessage(), 2000) : null,
        ]);
    }
}
