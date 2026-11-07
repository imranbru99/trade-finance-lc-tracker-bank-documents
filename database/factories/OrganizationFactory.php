<?php

namespace Database\Factories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->unique()->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'code' => strtoupper(fake()->unique()->bothify('ORG-####')),
            'email' => fake()->companyEmail(),
            'country' => 'Bangladesh',
            'currency' => 'USD',
            'timezone' => 'Asia/Dhaka',
            'status' => 'active',
        ];
    }
}
