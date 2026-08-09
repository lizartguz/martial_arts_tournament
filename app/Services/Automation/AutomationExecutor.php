<?php

namespace App\Services\Automation;

use App\AI\Contracts\AIProvider;
use App\AI\Data\AIRequest;
use App\Automation\AutomationRegistry;
use Illuminate\Support\Str;

class AutomationExecutor
{
    public function __construct(
        private readonly AIProvider $provider,
        private readonly AutomationRegistry $automationRegistry,
    ) {
    }

    public function execute(string $text): array
    {
        // Se normaliza una sola vez para que todas las automatizaciones comparen contra el mismo formato.
        $normalizedText = Str::of($text)
            ->lower()
            ->squish()
            ->value();

        // Primero se resuelve la automatizacion correcta y luego se consulta al proveedor de IA configurado.
        $automation = $this->automationRegistry->resolve($text, $normalizedText);

        $response = $this->provider->generateText(new AIRequest(
            instructions: $automation->instructions,
            input: $automation->prompt,
        ));

        return [
            'input' => $text,
            'normalized_input' => $normalizedText,
            'automation' => $automation->toArray(),
            'provider' => $response->provider,
            'model' => $response->model,
            'response' => $response->text,
        ];
    }
}
