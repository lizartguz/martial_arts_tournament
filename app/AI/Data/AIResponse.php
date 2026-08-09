<?php

namespace App\AI\Data;

class AIResponse
{
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly string $text,
        public readonly array $raw = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'model' => $this->model,
            'text' => $this->text,
            'raw' => $this->raw,
        ];
    }
}
