<?php

namespace App\Automation;

use App\Automation\Actions\IrrigationAdviceAutomationAction;
use App\Automation\Actions\WeeklyWeatherAutomationAction;
use App\Automation\Contracts\AutomationAction;
use App\Automation\Data\AutomationPayload;
use App\Automation\Exceptions\UnsupportedAutomationException;

class AutomationRegistry
{
    /**
     * @param  iterable<AutomationAction>  $actions
     */
    public function __construct(
        private readonly iterable $actions,
    ) {
    }

    public static function default(): self
    {
        // Registro central: aqui se agregan nuevas automatizaciones para exponerlas por el mismo endpoint.
        return new self([
            new WeeklyWeatherAutomationAction(),
            new IrrigationAdviceAutomationAction(),
        ]);
    }

    public function resolve(string $originalText, string $normalizedText): AutomationPayload
    {
        foreach ($this->actions as $action) {
            // La primera accion que soporte el texto normalizado sera la que se ejecute.
            if ($action->supports($normalizedText)) {
                return $action->buildPayload($originalText, $normalizedText);
            }
        }

        throw new UnsupportedAutomationException(sprintf(
            'No automation is configured for the input [%s].',
            $originalText,
        ));
    }
}
