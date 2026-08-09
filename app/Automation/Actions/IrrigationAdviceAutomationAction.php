<?php

namespace App\Automation\Actions;

use App\Automation\Actions\Concerns\MatchesSupportedTerms;
use App\Automation\Contracts\AutomationAction;
use App\Automation\Data\AutomationPayload;

class IrrigationAdviceAutomationAction implements AutomationAction
{
    use MatchesSupportedTerms;

    /**
     * Palabras clave o fragmentos que deben resolverse al prompt sobre riego.
     *
     * @var string[]
     */
    private array $supportedTerms = [
        'riego',
        'irrigacion',
        'irrigación',
    ];

    public function supports(string $normalizedText): bool
    {
        return $this->matchesSupportedTerms($normalizedText, $this->supportedTerms);
    }

    public function buildPayload(string $originalText, string $normalizedText): AutomationPayload
    {
        return new AutomationPayload(
            key: 'irrigation_advice',
            // Cada automatizacion define su propio prompt para agregar nuevos temas sin tocar el endpoint.
            instructions: 'Eres un asistente tecnico agricola. Responde en espanol claro, practico y ordenado, usando un tono profesional y facil de entender.',
            prompt: 'Explica de forma breve que es el riego, cuales son sus tipos principales, que beneficios aporta a la agricultura y que buenas practicas se deben seguir para usarlo eficientemente.',
            meta: [
                'trigger' => $originalText,
                'normalized_trigger' => $normalizedText,
                'topic' => 'concepto y recomendaciones sobre riego',
            ],
        );
    }
}
