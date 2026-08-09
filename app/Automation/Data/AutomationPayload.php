<?php

namespace App\Automation\Data;

class AutomationPayload
{
    public function __construct(
        public readonly string $key,
        public readonly string $instructions,
        public readonly string $prompt,
        public readonly array $meta = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'instructions' => $this->instructions,
            'prompt' => $this->prompt,
            'meta' => $this->meta,
        ];
    }
}
