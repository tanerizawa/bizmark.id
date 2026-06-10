<?php

namespace Database\Factories;

use App\Models\AISetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class AISettingFactory extends Factory
{
    protected $model = AISetting::class;

    public function definition(): array
    {
        return [
            'category' => 'global',
            'key' => $this->faker->unique()->word(),
            'value' => $this->faker->sentence(),
            'data_type' => 'string',
            'description' => $this->faker->sentence(),
            'is_public' => false,
            'is_encrypted' => false,
            'validation_rules' => null,
            'default_value' => null,
            'display_order' => 0,
            'group_name' => null,
            'requires_restart' => false,
            'created_by' => null,
            'updated_by' => null,
        ];
    }

    public function encrypted(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_encrypted' => true,
            'data_type' => 'string',
        ]);
    }

    public function provider(string $providerName): static
    {
        return $this->state(fn(array $attributes) => [
            'category' => 'provider',
            'group_name' => $providerName,
        ]);
    }
}
