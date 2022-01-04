<?php

namespace Database\Factories\Domains\Organization;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Domains\Organization\Models\OrganizationType;

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
