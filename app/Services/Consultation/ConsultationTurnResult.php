<?php

namespace App\Services\Consultation;

final readonly class ConsultationTurnResult
{
    public function __construct(
        public string $reply,
        public bool $readyForReport,
    ) {}
}
