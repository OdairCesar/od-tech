<?php

namespace App\Services\Consultation;

use App\Exceptions\AiGenerationException;

final class ConsultationReportParser
{
    public function parse(string $jsonPayload): GeneratedConsultationReport
    {
        $data = json_decode($jsonPayload, associative: true);

        if (! is_array($data)) {
            throw AiGenerationException::invalidResponseShape();
        }

        return new GeneratedConsultationReport(
            executiveSummary: $this->requireString($data, 'executive_summary'),
            problem: $this->requireString($data, 'problem'),
            objectives: $this->requireStringArray($data, 'objectives'),
            featuresEssential: $this->requireStringArray($data, 'features_essential'),
            featuresRecommended: $this->requireStringArray($data, 'features_recommended'),
            featuresFuture: $this->requireStringArray($data, 'features_future'),
            userProfiles: $this->requireStringArray($data, 'user_profiles'),
            currentFlow: $this->requireString($data, 'current_flow'),
            desiredFlow: $this->requireString($data, 'desired_flow'),
            integrations: $this->requireStringArray($data, 'integrations'),
            techStack: $this->requireStringArray($data, 'tech_stack'),
            complexity: $this->requireString($data, 'complexity'),
            complexityJustification: $this->requireString($data, 'complexity_justification'),
            mvp: $this->requireString($data, 'mvp'),
            nextPhases: $this->requireStringArray($data, 'next_phases'),
            estimateTimeframe: $this->requireString($data, 'estimate_timeframe'),
            estimateInvestment: $this->requireString($data, 'estimate_investment'),
            deliveryStages: $this->requireDeliveryStages($data),
            openQuestions: $this->requireStringArray($data, 'open_questions'),
        );
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<int, array{name: string, description: string, timeframe: string, investment: string}>
     */
    private function requireDeliveryStages(array $data): array
    {
        $value = $data['delivery_stages'] ?? null;

        if (! is_array($value)) {
            throw AiGenerationException::invalidResponseShape();
        }

        return array_values(array_map($this->requireDeliveryStage(...), $value));
    }

    /**
     * @return array{name: string, description: string, timeframe: string, investment: string}
     */
    private function requireDeliveryStage(mixed $stage): array
    {
        if (! is_array($stage)) {
            throw AiGenerationException::invalidResponseShape();
        }

        return [
            'name' => $this->requireString($stage, 'name'),
            'description' => $this->requireString($stage, 'description'),
            'timeframe' => $this->requireString($stage, 'timeframe'),
            'investment' => $this->requireString($stage, 'investment'),
        ];
    }

    /**
     * @param  array<array-key, mixed>  $data
     */
    private function requireString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        if (! is_string($value) || $value === '') {
            throw AiGenerationException::invalidResponseShape();
        }

        return $value;
    }

    /**
     * @param  array<array-key, mixed>  $data
     * @return array<int, string>
     */
    private function requireStringArray(array $data, string $key): array
    {
        $value = $data[$key] ?? null;

        if (! is_array($value)) {
            throw AiGenerationException::invalidResponseShape();
        }

        return array_values(array_map($this->requireStringValue(...), $value));
    }

    private function requireStringValue(mixed $value): string
    {
        if (! is_string($value) || $value === '') {
            throw AiGenerationException::invalidResponseShape();
        }

        return $value;
    }
}
