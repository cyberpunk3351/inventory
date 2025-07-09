<?php

namespace Database\Factories;

use App\Models\Device;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                    'MacBook Pro',
                    'Dell XPS 13',
                    'Surface Pro',
                    'ThinkPad X1',
                    'iPhone 14',
                    'Samsung Galaxy S23',
                    'iPad Pro',
                    'HP EliteBook',
                    'Lenovo Legion',
                    'ASUS ZenBook'
                ]) . ' - ' . $this->faker->unique()->numerify('###'),
            'first_deployed_at' => $this->faker->optional(0.8)->dateTimeBetween('-2 years', 'now'),
            'health_lifecycle_value' => $this->faker->numberBetween(1, 5),
            'health_lifecycle_unit' => $this->faker->randomElement(['year', 'month', 'day']),
        ];
    }

    public function notDeployed(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_deployed_at' => null,
        ]);
    }

    public function perfectHealth(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_deployed_at' => now()->subDays(30),
            'health_lifecycle_value' => 3,
            'health_lifecycle_unit' => 'year',
        ]);
    }

    public function goodHealth(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_deployed_at' => now()->subYears(1),
            'health_lifecycle_value' => 3,
            'health_lifecycle_unit' => 'year',
        ]);
    }

    public function fairHealth(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_deployed_at' => now()->subYears(2),
            'health_lifecycle_value' => 3,
            'health_lifecycle_unit' => 'year',
        ]);
    }

    public function poorHealth(): static
    {
        return $this->state(fn (array $attributes) => [
            'first_deployed_at' => now()->subYears(2)->subMonths(6),
            'health_lifecycle_value' => 3,
            'health_lifecycle_unit' => 'year',
        ]);
    }
}
