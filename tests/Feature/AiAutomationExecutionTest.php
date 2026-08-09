<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiAutomationExecutionTest extends TestCase
{
    public function test_it_executes_the_weekly_weather_automation_with_the_default_provider(): void
    {
        config()->set('ai.default', 'openai');
        config()->set('ai.providers.openai.api_key', 'test-key');
        config()->set('ai.providers.openai.model', 'gpt-4.1-mini');
        config()->set('ai.providers.openai.base_url', 'https://api.openai.com/v1');

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'model' => 'gpt-4.1-mini',
                'output_text' => 'El clima es el comportamiento promedio de las condiciones atmosfericas en una region durante largos periodos.',
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/automations/execute', [
            'text' => 'clima',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.provider', 'openai')
            ->assertJsonPath('data.automation.key', 'weather_concept')
            ->assertJsonPath('data.response', 'El clima es el comportamiento promedio de las condiciones atmosfericas en una region durante largos periodos.');

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->url() === 'https://api.openai.com/v1/responses'
                && $payload['model'] === 'gpt-4.1-mini'
                && $payload['input'] === 'Explica de forma breve que es el clima, cuales son sus elementos principales y por que es importante entenderlo en la vida diaria.';
        });
    }

    public function test_it_rejects_unknown_automation_keywords(): void
    {
        $response = $this->postJson('/api/v1/automations/execute', [
            'text' => 'inventario',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }

    public function test_it_executes_the_irrigation_automation(): void
    {
        config()->set('ai.default', 'openai');
        config()->set('ai.providers.openai.api_key', 'test-key');
        config()->set('ai.providers.openai.model', 'gpt-4.1-mini');
        config()->set('ai.providers.openai.base_url', 'https://api.openai.com/v1');

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'model' => 'gpt-4.1-mini',
                'output_text' => 'El riego es la aplicacion controlada de agua a los cultivos para cubrir sus necesidades hidricas y mejorar la produccion.',
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/automations/execute', [
            'text' => 'Quiero saber sobre riego para agricultura',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.provider', 'openai')
            ->assertJsonPath('data.automation.key', 'irrigation_advice')
            ->assertJsonPath('data.response', 'El riego es la aplicacion controlada de agua a los cultivos para cubrir sus necesidades hidricas y mejorar la produccion.');

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->url() === 'https://api.openai.com/v1/responses'
                && $payload['model'] === 'gpt-4.1-mini'
                && $payload['input'] === 'Explica de forma breve que es el riego, cuales son sus tipos principales, que beneficios aporta a la agricultura y que buenas practicas se deben seguir para usarlo eficientemente.';
        });
    }

    public function test_it_matches_weather_automation_from_a_phrase(): void
    {
        config()->set('ai.default', 'openai');
        config()->set('ai.providers.openai.api_key', 'test-key');
        config()->set('ai.providers.openai.model', 'gpt-4.1-mini');
        config()->set('ai.providers.openai.base_url', 'https://api.openai.com/v1');

        Http::fake([
            'https://api.openai.com/v1/responses' => Http::response([
                'model' => 'gpt-4.1-mini',
                'output_text' => 'El clima describe el promedio de condiciones atmosfericas de una zona durante largos periodos.',
            ], 200),
        ]);

        $response = $this->postJson('/api/v1/automations/execute', [
            'text' => 'Explicame el clima por favor',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.automation.key', 'weather_concept');
    }
}
