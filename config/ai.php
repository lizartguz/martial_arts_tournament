<?php

return [
    'default' => env('AI_PROVIDER', 'openai'),

    'timeout' => (int) env('AI_TIMEOUT', 60),

    'providers' => [
        'openai' => [
            'driver' => 'openai',
            'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
            'api_key' => env('OPENAI_API_KEY'),
            // Modelo por defecto para texto.
            // Puedes cambiarlo segun costo, velocidad o capacidad.
            // Ejemplos utiles de OpenAI:
            // - gpt-5.2: opcion mas capaz para tareas complejas, razonamiento y flujos exigentes.
            // - gpt-5.1: muy buena opcion general si quieres calidad alta con razonamiento configurable.
            // - gpt-5-mini: mas rapido y economico para automatizaciones bien definidas.
            // - gpt-5-nano: el mas liviano y barato para tareas simples y de alto volumen.
            // - gpt-4.1-mini: alternativa estable y economica para casos sencillos.
            // - gpt-4.1: alternativa solida si prefieres un modelo no orientado a razonamiento.
            // - gpt-4o-mini: buena opcion si luego quieres mezclar texto con vision a menor costo.
            // - gpt-4o: mas capaz para tareas multimodales o interacciones mas exigentes.
            'model' => env('OPENAI_MODEL', 'gpt-4.1-mini'),
        ],

        'google' => [
            'driver' => 'google',
            'api_key' => env('GOOGLE_AI_API_KEY'),
            'model' => env('GOOGLE_AI_MODEL', 'gemini-2.5-flash'),
        ],
    ],
];
