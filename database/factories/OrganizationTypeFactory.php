<?php

namespace Database\Factories;

use App\Domains\Organization\Models\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationTypeFactory extends Factory
{
    protected $model = OrganizationType::class;

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
