<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ImportLc;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ImportLc>
 */
class ImportLcFactory extends Factory
{
    protected $model = ImportLc::class;

    public function definition(): array
    {
        $organization = Organization::factory();

        return [
            'organization_id' => $organization,
            'company_id' => Company::factory()->for($organization),
            'lc_number' => strtoupper(fake()->unique()->bothify('ILC-########')),
            'applicant_name' => fake()->company(),
            'beneficiary_name' => fake()->company(),
            'currency' => 'USD',
            'lc_amount' => fake()->randomFloat(2, 10_000, 500_000),
            'expiry_date' => now()->addMonths(3),
            'latest_shipment_date' => now()->addMonths(2),
            'presentation_period_days' => 21,
            'status' => 'Active',
        ];
    }
}
