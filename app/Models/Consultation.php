<?php

namespace App\Models;

use App\Enums\ConsultationStatus;
use Database\Factories\ConsultationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property array{project_type: string, current_process: string, main_difficulty: string}|null $initial_answers
 * @property array<int, array{role: string, content: string}> $messages
 * @property ConsultationStatus $status
 * @property array<string, mixed>|null $report
 */
class Consultation extends Model
{
    /** @use HasFactory<ConsultationFactory> */
    use HasFactory;

    public const int MAX_FOLLOWUP_QUESTIONS = 12;

    /** @var list<string> */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'initial_answers',
        'messages',
        'status',
        'questions_asked',
        'report',
        'ai_model',
        'ai_error',
        'source_url',
        'read_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'initial_answers' => 'array',
            'messages' => 'array',
            'status' => ConsultationStatus::class,
            'report' => 'array',
            'read_at' => 'datetime',
        ];
    }
}
