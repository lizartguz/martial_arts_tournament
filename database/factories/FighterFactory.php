<?php

namespace Database\Factories;

use App\Models\Fighter;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Fighter>
 */
class FighterFactory extends Factory
{
    protected $model = Fighter::class;

    public function definition(): array
    {
        $firstName = fake()->unique()->firstName();
        $lastName = fake()->lastName();

        return [
            'uuid' => (string) Str::uuid(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'slug' => Str::slug($firstName.' '.$lastName.'-'.fake()->unique()->numberBetween(1, 100000)),
            'gender' => fake()->randomElement(['male', 'female']),
            'status' => 1,
        ];
    }
}
