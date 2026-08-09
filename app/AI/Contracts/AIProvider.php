<?php

namespace App\AI\Contracts;

use App\AI\Data\AIRequest;
use App\AI\Data\AIResponse;

interface AIProvider
{
    public function generateText(AIRequest $request): AIResponse;

    public function name(): string;
}
