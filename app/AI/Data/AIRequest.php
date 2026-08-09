<?php

namespace App\AI\Data;

class AIRequest
{
    public function __construct(
        public readonly string $instructions,
        public readonly string $input,
        public readonly ?string $model = null,
    ) {
    }
}
