<?php

namespace Database\Factories\Domains\Organization;

use App\Domains\Organization\Models\Interest;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterestFactory extends Factory
{
    protected $model = Interest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word(),
        ];
    }
}
