<?php

namespace App\Services\Portfolio;

use App\Exceptions\AiGenerationException;
use App\Services\Ai\JsonSchema;
use App\Services\Ai\TextGenerator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

final class PortfolioCopyGenerator
{
    public function __construct(private readonly TextGenerator $textGenerator) {}

    /**
     * @return array{title: string, excerpt: string}
     */
    public function generate(string $url, ?string $serviceTitle): array
    {
        $pageText = $this->fetchPageText($url);

        $result = $this->textGenerator->generate(
            [
                ['role' => 'system', 'content' => $this->systemPrompt()],
                ['role' => 'user', 'content' => $this->userPrompt($pageText, $serviceTitle)],
            ],
            $this->responseFormat(),
            0.7,
        );

        return $this->parse($result->content);
    }

    private function fetchPageText(string $url): string
    {
        try {
            $response = Http::timeout(15)->get($url);
        } catch (Throwable $exception) {
            throw AiGenerationException::unreachableUrl($exception);
        }

        if (! $response->successful()) {
            throw AiGenerationException::unreachableUrl();
        }

        return Str::of(strip_tags($response->body()))
            ->squish()
            ->limit(4000)
            ->toString();
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
            Você é um redator de portfólio para a OD Tec, uma empresa de tecnologia. A partir do texto extraído
            da página de um projeto entregue, escreva um título curto e um resumo (excerpt) convincente em
            português do Brasil, destacando o valor entregue ao cliente. Responda estritamente no formato
            JSON solicitado.
            PROMPT;
    }

    private function userPrompt(string $pageText, ?string $serviceTitle): string
    {
        $lines = [];

        if ($serviceTitle) {
            $lines[] = "Serviço prestado: {$serviceTitle}";
        }

        $lines[] = "Conteúdo extraído da página do projeto:\n{$pageText}";

        return implode("\n", $lines);
    }

    private function responseFormat(): JsonSchema
    {
        return new JsonSchema(
            name: 'portfolio_copy',
            schema: [
                'type' => 'object',
                'required' => ['title', 'excerpt'],
                'properties' => [
                    'title' => ['type' => 'string'],
                    'excerpt' => ['type' => 'string'],
                ],
            ],
        );
    }

    /**
     * @return array{title: string, excerpt: string}
     */
    private function parse(string $jsonPayload): array
    {
        $data = json_decode($jsonPayload, associative: true);

        if (! is_array($data)) {
            throw AiGenerationException::invalidResponseShape();
        }

        $title = $data['title'] ?? null;
        $excerpt = $data['excerpt'] ?? null;

        if (! is_string($title) || $title === '' || ! is_string($excerpt) || $excerpt === '') {
            throw AiGenerationException::invalidResponseShape();
        }

        return ['title' => $title, 'excerpt' => $excerpt];
    }
}
