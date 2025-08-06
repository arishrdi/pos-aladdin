<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Inventory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'price' => $this->faker->randomFloat(0, 1000, 10000),
            'sku' => 'SKU-' . $this->generateBigRandomNumber(7),
            'description' => $this->faker->sentence(),
            'image' => 'products/gambar.jpg',
            'category_id' => $this->faker->numberBetween(1, 5),
            'is_active' => $this->faker->boolean(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => $this->faker->numberBetween(1, 100),
                'min_stock' => $this->faker->numberBetween(1, 20),
                'outlet_id' => $this->faker->numberBetween(1, 4),
            ]);
        });
    }

    /**
     * Generates a random number with exactly $digits digits
     */
    private function generateBigRandomNumber(int $digits): string
    {
        $result = '';
        for ($i = 0; $i < $digits; $i++) {
            $result .= random_int(0, 9);
        }
        return $result;
    }
}