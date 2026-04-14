<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'amount' => fake()->randomFloat(2, 100, 50000),
            'product_name' => fake()->randomElement([
                'Wireless Headphones',
                'Smart Watch',
                'Running Shoes',
                'Laptop Stand',
                'Bluetooth Speaker',
                'Phone Case',
                'USB Hub',
                'Desk Lamp',
            ]),
            'transaction_id' => 'PUR_' . strtoupper(Str::random(8)),
        ];
    }
}
