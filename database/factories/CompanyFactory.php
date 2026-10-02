<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\Customer;
use App\Models\TaxLicense;
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

    /** Give the company a currently valid Tax license. */
    public function licensed(): static
    {
        return $this->afterCreating(fn (Company $company) => TaxLicense::factory()->create(['company_id' => $company->id]));
    }

    /** Attach the owner as an `owner` member so tenancy sees the company. */
    public function configure(): static
    {
        return $this->afterCreating(function (Company $company) {
            $company->members()->syncWithoutDetaching([$company->owner_id => ['role' => 'owner']]);
        });
    }
}
