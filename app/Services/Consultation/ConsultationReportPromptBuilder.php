<?php

namespace App\Services\Consultation;

use App\Models\Consultation;
use App\Services\Ai\JsonSchema;
use LogicException;

final class ConsultationReportPromptBuilder
{
    /**
     * @return array{system: string, user: string}
     */
    public function build(Consultation $consultation): array
    {
        return [
            'system' => $this->systemPrompt(),
            'user' => $this->userPrompt($consultation),
        ];
    }

    public function responseFormat(): JsonSchema
    {
        $stringArray = ['type' => 'array', 'items' => ['type' => 'string']];

        $deliveryStages = [
            'type' => 'array',
            'items' => [
                'type' => 'object',
                'required' => ['name', 'description', 'timeframe', 'investment'],
                'properties' => [
                    'name' => ['type' => 'string'],
                    'description' => ['type' => 'string'],
                    'timeframe' => ['type' => 'string'],
                    'investment' => ['type' => 'string'],
                ],
            ],
        ];

        return new JsonSchema(
            name: 'consultation_report',
            schema: [
                'type' => 'object',
                'required' => [
                    'executive_summary', 'problem', 'objectives', 'features_essential',
                    'features_recommended', 'features_future', 'user_profiles', 'current_flow',
                    'desired_flow', 'integrations', 'tech_stack', 'complexity',
                    'complexity_justification', 'mvp', 'next_phases', 'estimate_timeframe',
                    'estimate_investment', 'delivery_stages', 'open_questions',
                ],
                'properties' => [
                    'executive_summary' => ['type' => 'string'],
                    'problem' => ['type' => 'string'],
                    'objectives' => $stringArray,
                    'features_essential' => $stringArray,
                    'features_recommended' => $stringArray,
                    'features_future' => $stringArray,
                    'user_profiles' => $stringArray,
                    'current_flow' => ['type' => 'string'],
                    'desired_flow' => ['type' => 'string'],
                    'integrations' => $stringArray,
                    'tech_stack' => $stringArray,
                    'complexity' => ['type' => 'string', 'enum' => ['baixa', 'media', 'alta', 'muito_alta']],
                    'complexity_justification' => ['type' => 'string'],
                    'mvp' => ['type' => 'string'],
                    'next_phases' => $stringArray,
                    'estimate_timeframe' => ['type' => 'string'],
                    'estimate_investment' => ['type' => 'string'],
                    'delivery_stages' => $deliveryStages,
                    'open_questions' => $stringArray,
                ],
            ],
        );
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
            Você é um Analista de Negócios sênior da OD Tec. Você acabou de concluir uma entrevista com um
            cliente sobre uma ideia de projeto de software e agora deve produzir um relatório estruturado
            em português do Brasil, com linguagem simples nas seções voltadas ao cliente. Não faça perguntas,
            apenas analise a transcrição recebida e produza o relatório.

            No campo tech_stack, sugira tecnologias reais e adequadas ao projeto (ex: Laravel, Next.js,
            React Native, PostgreSQL, Redis) — esse campo é de uso interno da equipe técnica da OD Tec.

            Antes de estimar, avalie se a ideia do cliente já é bem atendida por uma solução pronta e
            amplamente conhecida no mercado (ex: Nuvemshop, Shopify ou WooCommerce para e-commerce; Calendly
            para agendamento). Se for o caso — e principalmente se isso foi mencionado na transcrição —, o
            escopo passa a ser configuração e customização dessa plataforma (tema, cadastro de produtos,
            integrações de pagamento) em vez de construir do zero. Nesse cenário: cite a(s) plataforma(s)
            pronta(s) em tech_stack, reflita isso em mvp e complexity_justification, e reduza
            estimate_timeframe, estimate_investment e delivery_stages de acordo (as etapas passam a ser de
            configuração/customização, não de desenvolvimento do zero). Se não existir solução pronta
            adequada, ignore esta orientação e trate como um projeto sob medida normalmente.

            No campo complexity, use exatamente um dos valores: baixa, media, alta, muito_alta.

            No campo delivery_stages, quebre APENAS a construção do MVP (o mesmo escopo descrito no campo mvp)
            em etapas internas de execução — sprints/blocos de trabalho do mesmo produto sendo construído uma
            única vez (ex: "Configuração inicial e autenticação", "Cadastro de produtos e clientes", "Fluxo de
            pedidos e pagamentos", "Testes e implantação"). As etapas são fatias sequenciais do MESMO
            desenvolvimento, cobradas uma vez, não fases comerciais diferentes. NUNCA use como nome de etapa
            termos como "protótipo", "MVP" ou "versão completa/profissional" como se fossem produtos ou
            entregas separadas — isso dá a entender que o cliente pagaria múltiplas vezes pela mesma coisa, o
            que é errado. A soma dos prazos e investimentos das etapas deve corresponder ao mesmo total já
            informado em estimate_timeframe e estimate_investment (são o mesmo escopo dividido, não escopos
            adicionais). Gere entre 3 e 5 etapas. Recursos além do MVP continuam apenas em next_phases, como
            fases comerciais futuras e separadas — não misture com delivery_stages. Não inclua nenhuma
            estimativa de tamanho de equipe — a execução é sempre feita pela equipe da OD Tec.

            Responda estritamente no formato JSON solicitado.
            PROMPT;
    }

    private function userPrompt(Consultation $consultation): string
    {
        $answers = $consultation->initial_answers;

        if (! is_array($answers)) {
            throw new LogicException('A consulta ainda não possui as respostas iniciais.');
        }

        $lines = [
            "O que o cliente gostaria de criar: {$answers['project_type']}",
            "Como o cliente faz isso hoje: {$answers['current_process']}",
            "Maior dificuldade atual do cliente: {$answers['main_difficulty']}",
            '',
            'Transcrição da entrevista:',
        ];

        foreach ($consultation->messages as $turn) {
            $speaker = $turn['role'] === 'assistant' ? 'Analista' : 'Cliente';
            $lines[] = "{$speaker}: {$turn['content']}";
        }

        return implode("\n", $lines);
    }
}
