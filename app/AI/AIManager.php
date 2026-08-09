<?php

namespace App\AI;

use App\AI\Contracts\AIProvider;
use App\AI\Exceptions\AIProviderException;
use App\AI\Providers\OpenAIProvider;

class AIManager
{
    public function __construct(
        private readonly array $config,
    ) {
    }

    public function driver(?string $name = null): AIProvider
    {
        $providerName = $name ?: ($this->config['default'] ?? 'openai');
        $providerConfig = $this->config['providers'][$providerName] ?? null;

        if (!is_array($providerConfig)) {
            throw new AIProviderException(sprintf('AI provider [%s] is not configured.', $providerName));
        }

        // La seleccion del proveedor depende de configuracion para cambiar de IA sin tocar rutas ni controladores.
        return match ($providerConfig['driver'] ?? $providerName) {
            'openai' => new OpenAIProvider(
                config: $providerConfig,
                timeout: (int) ($this->config['timeout'] ?? 60),
            ),
            default => throw new AIProviderException(sprintf(
                'AI provider driver [%s] is not supported yet.',
                $providerConfig['driver'] ?? $providerName,
            )),
        };
    }
}
