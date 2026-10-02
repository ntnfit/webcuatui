<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'owner_id' => Customer::factory(),
            'name' => fake()->company(),
            'mst' => fake()->unique()->numerify('##########'),
            'address' => fake()->address(),
        ];
    }

    /** Attach the owner as an `owner` member so tenancy sees the company. */
    public function configure(): static
    {
        return $this->afterCreating(function (Company $company) {
            $company->members()->syncWithoutDetaching([$company->owner_id => ['role' => 'owner']]);
        });
    }
}
