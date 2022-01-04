<?php

namespace Database\Factories\Domains\Organization;

use App\Domains\Organization\Models\Interest;
use App\Domains\Organization\Models\Organization;
use App\Domains\Organization\Models\OrganizationType;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'fantasy_name'          => $this->faker->company(),
            'corporate_name'        => $this->faker->unique()->company(),
            'domain'                => $this->faker->unique()->slug(),
            'cpf_cnpj'              => rand(0, 1) ? $this->faker->unique()->cpf(false) : $this->faker->unique()->cnpj(false),
            'logo'                  => null,
            'social_contract'       => null,
            'organization_type_id'  => OrganizationType::factory(),
            'interests'             => Interest::factory(2)->create()->pluck('name')->toArray(),
        ];
    }
}
