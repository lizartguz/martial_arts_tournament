<?php

namespace App\Automation\Actions\Concerns;

trait MatchesSupportedTerms
{
    /**
     * @param  string[]  $supportedTerms
     */
    protected function matchesSupportedTerms(string $normalizedText, array $supportedTerms): bool
    {
        foreach ($supportedTerms as $term) {
            // La coincidencia exacta mantiene rapidos y predecibles los disparadores simples.
            if ($normalizedText === $term) {
                return true;
            }

            // La coincidencia por frase permite reutilizar la misma accion con textos mas naturales.
            if (str_contains($normalizedText, $term)) {
                return true;
            }
        }

        return false;
    }
}
