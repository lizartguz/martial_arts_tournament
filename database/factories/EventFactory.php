<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $name = 'Combate Real '.fake()->unique()->numberBetween(1, 100000);

        return [
            'uuid' => (string) Str::uuid(),
            'name' => $name,
            'slug' => Str::slug($name),
            'venue_id' => null,
            'starts_at' => fake()->dateTimeBetween('-1 month', '+2 months'),
            'status' => 1,
            'is_featured' => false,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 1]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 0]);
    }
}
