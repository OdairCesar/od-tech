<?php

namespace App\Services\Consultation;

use App\Enums\ConsultationStatus;
use App\Exceptions\AiGenerationException;
use App\Models\Consultation;
use App\Services\Ai\TextGenerator;

final class ConsultationChatService
{
    public function __construct(
        private readonly ConsultationPromptBuilder $promptBuilder,
        private readonly TextGenerator $textGenerator,
    ) {}

    public function startInterview(Consultation $consultation): void
    {
        $result = $this->requestTurn($consultation);

        $consultation->update([
            'messages' => [
                ['role' => 'assistant', 'content' => $result->reply],
            ],
        ]);
    }

    public function sendTurn(Consultation $consultation, string $userMessage): void
    {
        $messages = $consultation->messages;
        $messages[] = ['role' => 'user', 'content' => $userMessage];
        $consultation->update(['messages' => $messages]);

        $result = $this->requestTurn($consultation);

        $questionsAsked = $consultation->questions_asked + 1;
        $readyForReport = $result->readyForReport || $questionsAsked >= Consultation::MAX_FOLLOWUP_QUESTIONS;

        $messages[] = ['role' => 'assistant', 'content' => $result->reply];

        $consultation->update([
            'messages' => $messages,
            'questions_asked' => $questionsAsked,
            'status' => $readyForReport ? ConsultationStatus::GeneratingReport : ConsultationStatus::InProgress,
        ]);
    }

    private function requestTurn(Consultation $consultation): ConsultationTurnResult
    {
        $result = $this->textGenerator->generate(
            $this->promptBuilder->messagesFor($consultation),
            $this->promptBuilder->responseFormat(),
            0.6,
        );

        return $this->parse($result->content);
    }

    private function parse(string $jsonPayload): ConsultationTurnResult
    {
        $data = json_decode($jsonPayload, associative: true);

        if (! is_array($data)) {
            throw AiGenerationException::invalidResponseShape();
        }

        $reply = $data['reply'] ?? null;

        if (! is_string($reply) || $reply === '') {
            throw AiGenerationException::invalidResponseShape();
        }

        return new ConsultationTurnResult(
            reply: $reply,
            readyForReport: (bool) ($data['ready_for_report'] ?? false),
        );
    }
}
