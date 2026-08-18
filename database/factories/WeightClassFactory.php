<?php

namespace Database\Factories;

use App\Models\WeightClass;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeightClass>
 */
class WeightClassFactory extends Factory
{
    protected $model = WeightClass::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true).' weight';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'gender' => 'mixed',
            'display_order' => fake()->numberBetween(1, 20),
            'status' => 1,
        ];
    }
}
