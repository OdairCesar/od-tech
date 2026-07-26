<?php

namespace Database\Factories;

use App\Enums\ConsultationStatus;
use App\Models\Consultation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Consultation>
 */
class ConsultationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'initial_answers' => null,
            'messages' => [],
            'status' => ConsultationStatus::InProgress,
            'questions_asked' => 0,
            'report' => null,
            'ai_model' => null,
            'ai_error' => null,
            'source_url' => $this->faker->url(),
            'read_at' => null,
        ];
    }

    public function withInitialAnswers(): self
    {
        return $this->state(fn (): array => [
            'initial_answers' => [
                'project_type' => 'Um sistema para minha empresa',
                'current_process' => 'Hoje recebemos pedidos pelo WhatsApp e anotamos em uma planilha.',
                'main_difficulty' => 'Perdemos muito tempo e esquecemos pedidos.',
            ],
        ]);
    }

    public function generatingReport(): self
    {
        return $this->state(fn (): array => [
            'status' => ConsultationStatus::GeneratingReport,
            'questions_asked' => Consultation::MAX_FOLLOWUP_QUESTIONS,
        ]);
    }

    public function completed(): self
    {
        return $this->state(fn (): array => [
            'status' => ConsultationStatus::Completed,
            'ai_model' => 'gpt-4.1',
            'report' => [
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
                    [
                        'name' => 'Fluxo de pedidos e testes finais',
                        'description' => 'Fechamento de pedidos, testes e implantação.',
                        'timeframe' => '3 semanas',
                        'investment' => 'R$ 10.000',
                    ],
                ],
                'open_questions' => ['Pergunta em aberto 1'],
            ],
        ]);
    }
}
