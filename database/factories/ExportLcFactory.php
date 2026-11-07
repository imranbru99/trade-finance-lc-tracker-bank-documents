<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\ExportLc;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExportLc>
 */
class ExportLcFactory extends Factory
{
    protected $model = ExportLc::class;

    public function definition(): array
    {
        $organization = Organization::factory();

        return [
            'organization_id' => $organization,
            'company_id' => Company::factory()->for($organization),
            'lc_number' => strtoupper(fake()->unique()->bothify('ELC-########')),
            'buyer_name' => fake()->company(),
            'currency' => 'USD',
            'export_value' => fake()->randomFloat(2, 10_000, 500_000),
            'expiry_date' => now()->addMonths(3),
            'latest_shipment_date' => now()->addMonths(2),
            'status' => 'Active',
        ];
    }
}
