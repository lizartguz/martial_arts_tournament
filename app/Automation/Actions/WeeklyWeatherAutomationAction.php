<?php

namespace App\Automation\Actions;

use App\Automation\Actions\Concerns\MatchesSupportedTerms;
use App\Automation\Contracts\AutomationAction;
use App\Automation\Data\AutomationPayload;

class WeeklyWeatherAutomationAction implements AutomationAction
{
    use MatchesSupportedTerms;

    /**
     * Palabras clave o fragmentos que deben resolverse al prompt sobre clima.
     *
     * @var string[]
     */
    private array $supportedTerms = [
        'clima',
        'weather',
        'pronostico',
        'pronóstico',
        'tiempo',
    ];

    public function supports(string $normalizedText): bool
    {
        return $this->matchesSupportedTerms($normalizedText, $this->supportedTerms);
    }

    public function buildPayload(string $originalText, string $normalizedText): AutomationPayload
    {
        return new AutomationPayload(
            key: 'weather_concept',
            // Las instrucciones definen el comportamiento del asistente por separado del prompt principal.
            instructions: 'Eres un asistente educativo especializado en clima y meteorologia. Responde en espanol claro, breve y facil de entender, con un tono profesional.',
            prompt: 'Explica de forma breve que es el clima, cuales son sus elementos principales y por que es importante entenderlo en la vida diaria.',
            meta: [
                'trigger' => $originalText,
                'normalized_trigger' => $normalizedText,
                'topic' => 'concepto sobre el clima',
            ],
        );
    }
}
