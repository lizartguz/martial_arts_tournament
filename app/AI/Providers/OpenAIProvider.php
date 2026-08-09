<?php

namespace App\AI\Providers;

use App\AI\Contracts\AIProvider;
use App\AI\Data\AIRequest;
use App\AI\Data\AIResponse;
use App\AI\Exceptions\AIProviderException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class OpenAIProvider implements AIProvider
{
    public function __construct(
        private readonly array $config,
        private readonly int $timeout,
    ) {
    }

    public function generateText(AIRequest $request): AIResponse
    {
        $apiKey = $this->config['api_key'] ?? null;

        if (blank($apiKey)) {
            throw new AIProviderException('OpenAI API key is not configured.');
        }

        $model = $request->model ?: ($this->config['model'] ?? 'gpt-4.1-mini');

        // La aplicacion usa la Responses API de OpenAI, que es la via recomendada para integraciones nuevas.
        $response = Http::baseUrl(rtrim($this->config['base_url'] ?? 'https://api.openai.com/v1', '/'))
            ->timeout($this->timeout)
            ->acceptJson()
            ->asJson()
            ->withToken($apiKey)
            ->post('/responses', [
                'model' => $model,
                'instructions' => $request->instructions,
                'input' => $request->input,
            ]);

        if ($response->failed()) {
            $message = $response->json('error.message')
                ?? $response->body()
                ?? 'OpenAI request failed.';

            throw new AIProviderException($message);
        }

        $payload = $response->json();
        $text = $this->extractText($payload);

        if (blank($text)) {
            throw new AIProviderException('OpenAI response did not contain generated text.');
        }

        return new AIResponse(
            provider: $this->name(),
            model: $payload['model'] ?? $model,
            text: $text,
            raw: $payload,
        );
    }

    public function name(): string
    {
        return 'openai';
    }

    private function extractText(array $payload): string
    {
        // Primero se intenta leer el texto resumido y, si no existe, se recorre la salida estructurada.
        $outputText = trim((string) ($payload['output_text'] ?? ''));

        if ($outputText !== '') {
            return $outputText;
        }

        $segments = [];

        foreach (($payload['output'] ?? []) as $outputItem) {
            foreach (($outputItem['content'] ?? []) as $contentItem) {
                $type = $contentItem['type'] ?? null;

                if ($type === 'output_text') {
                    $segments[] = $contentItem['text'] ?? '';
                    continue;
                }

                $nestedText = Arr::get($contentItem, 'text.value');
                if (is_string($nestedText) && $nestedText !== '') {
                    $segments[] = $nestedText;
                }
            }
        }

        return trim(implode("\n", array_filter($segments)));
    }
}
