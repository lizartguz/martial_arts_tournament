<?php

namespace App\Automation\Contracts;

use App\Automation\Data\AutomationPayload;

interface AutomationAction
{
    public function supports(string $normalizedText): bool;

    public function buildPayload(string $originalText, string $normalizedText): AutomationPayload;
}
