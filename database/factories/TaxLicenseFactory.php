<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\TaxLicense;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxLicense>
 */
class TaxLicenseFactory extends Factory
{
    protected $model = TaxLicense::class;

    /** Valid for a year, started yesterday. */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addYear(),
            'status' => TaxLicense::STATUS_ACTIVE,
        ];
    }

    public function expired(): static
    {
        return $this->state(['starts_at' => now()->subYear(), 'expires_at' => now()->subDay()]);
    }

    public function revoked(): static
    {
        return $this->state(['status' => TaxLicense::STATUS_REVOKED]);
    }
}
