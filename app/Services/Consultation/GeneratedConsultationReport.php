<?php

namespace App\Services\Consultation;

final readonly class GeneratedConsultationReport
{
    /**
     * @param  array<int, string>  $objectives
     * @param  array<int, string>  $featuresEssential
     * @param  array<int, string>  $featuresRecommended
     * @param  array<int, string>  $featuresFuture
     * @param  array<int, string>  $userProfiles
     * @param  array<int, string>  $integrations
     * @param  array<int, string>  $techStack
     * @param  array<int, string>  $nextPhases
     * @param  array<int, array{name: string, description: string, timeframe: string, investment: string}>  $deliveryStages
     * @param  array<int, string>  $openQuestions
     */
    public function __construct(
        public string $executiveSummary,
        public string $problem,
        public array $objectives,
        public array $featuresEssential,
        public array $featuresRecommended,
        public array $featuresFuture,
        public array $userProfiles,
        public string $currentFlow,
        public string $desiredFlow,
        public array $integrations,
        public array $techStack,
        public string $complexity,
        public string $complexityJustification,
        public string $mvp,
        public array $nextPhases,
        public string $estimateTimeframe,
        public string $estimateInvestment,
        public array $deliveryStages,
        public array $openQuestions,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'executive_summary' => $this->executiveSummary,
            'problem' => $this->problem,
            'objectives' => $this->objectives,
            'features_essential' => $this->featuresEssential,
            'features_recommended' => $this->featuresRecommended,
            'features_future' => $this->featuresFuture,
            'user_profiles' => $this->userProfiles,
            'current_flow' => $this->currentFlow,
            'desired_flow' => $this->desiredFlow,
            'integrations' => $this->integrations,
            'tech_stack' => $this->techStack,
            'complexity' => $this->complexity,
            'complexity_justification' => $this->complexityJustification,
            'mvp' => $this->mvp,
            'next_phases' => $this->nextPhases,
            'estimate_timeframe' => $this->estimateTimeframe,
            'estimate_investment' => $this->estimateInvestment,
            'delivery_stages' => $this->deliveryStages,
            'open_questions' => $this->openQuestions,
        ];
    }
}
