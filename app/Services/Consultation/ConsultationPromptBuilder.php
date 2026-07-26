<?php

namespace App\Services\Consultation;

use App\Models\Consultation;
use LogicException;

final class ConsultationPromptBuilder
{
    /**
     * @return array<int, array{role: string, content: string}>
     */
    public function messagesFor(Consultation $consultation): array
    {
        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user', 'content' => $this->contextMessage($consultation)],
        ];

        foreach ($consultation->messages as $turn) {
            $messages[] = ['role' => $turn['role'], 'content' => $turn['content']];
        }

        return $messages;
    }

    /**
     * @return array<string, mixed>
     */
    public function responseFormat(): array
    {
        return [
            'type' => 'json_schema',
            'json_schema' => [
                'name' => 'consultation_turn',
                'strict' => true,
                'schema' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['reply', 'ready_for_report'],
                    'properties' => [
                        'reply' => ['type' => 'string'],
                        'ready_for_report' => ['type' => 'boolean'],
                    ],
                ],
            ],
        ];
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
            Você é um Analista de Negócios especializado em levantamento de requisitos para projetos de
            software, atuando pela OD Tec. Seu papel é conversar com o cliente para entender completamente
            sua ideia antes de sugerir qualquer solução. Você atua como Analista de Negócios, Product Owner
            e Consultor de Transformação Digital — nunca como desenvolvedor.

            Regras obrigatórias:
            - Faça apenas uma pergunta por vez, curta e em português do Brasil.
            - Nunca utilize termos técnicos (não pergunte sobre telas, API, banco de dados, framework ou
              arquitetura). Pergunte sempre sobre o funcionamento do negócio.
            - Adapte cada pergunta ao contexto da conversa até então. Nunca existe um roteiro fixo.
            - Evite perguntas repetidas ou desnecessárias.
            - Durante a conversa, procure descobrir: objetivo principal, problema a resolver, público-alvo,
              fluxo atual e desejado do negócio, funcionalidades principais e opcionais, integrações
              necessárias, perfis de usuários, riscos, complexidade, escalabilidade e a melhor estratégia
              de MVP.
            - Se identificar que a ideia do cliente já é bem atendida por soluções prontas e amplamente
              conhecidas no mercado (ex: Nuvemshop, Shopify ou WooCommerce para e-commerce; Calendly para
              agendamento), comente isso naturalmente dentro de uma das suas perguntas, deixando claro que
              customizar uma plataforma pronta costuma ser bem mais rápido e barato do que construir do
              zero. Não transforme isso em uma pergunta formal separada nem exija que o cliente escolha
              ali — apenas registre a preferência dele caso comente algo a respeito.
            - Quando já tiver informação suficiente para elaborar um relatório consistente, marque
              ready_for_report como true e envie uma mensagem curta de encerramento agradecendo o cliente.
            - Enquanto ready_for_report for false, o campo reply deve conter sempre a próxima pergunta.

            Responda estritamente no formato JSON solicitado.
            PROMPT;
    }

    private function contextMessage(Consultation $consultation): string
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
            "Você já fez {$consultation->questions_asked} de no máximo ".Consultation::MAX_FOLLOWUP_QUESTIONS.' perguntas nesta entrevista. Se estiver perto do limite, encaminhe a conversa para o encerramento.',
        ];

        return implode("\n", $lines);
    }
}
