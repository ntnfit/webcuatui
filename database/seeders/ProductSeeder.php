<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = ['Electronics', 'Clothing', 'Books', 'Home & Garden', 'Sports', 'Toys', 'Beauty', 'Food'];
        $adjectives = ['Premium', 'Professional', 'Deluxe', 'Ultimate', 'Advanced', 'Classic', 'Modern', 'Vintage'];
        $products = ['Laptop', 'Phone', 'Watch', 'Camera', 'Headphones', 'Keyboard', 'Mouse', 'Monitor', 'Tablet', 'Speaker'];
        
        for ($i = 1; $i <= 100; $i++) {
            $name = $adjectives[array_rand($adjectives)] . ' ' . $products[array_rand($products)] . ' ' . $i;
            $price = rand(100000, 50000000); // 100k to 50M VND
            $hasSale = rand(0, 1);
            $salePrice = $hasSale ? $price - ($price * rand(10, 50) / 100) : null;
            
            Product::create([
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => 'High-quality ' . strtolower($name) . ' with excellent features and performance. Perfect for both personal and professional use. Comes with warranty and customer support.',
                'price' => $price,
                'sale_price' => $salePrice,
                'quantity' => rand(0, 100),
                'image' => null,
                'status' => rand(0, 10) > 1 ? 'active' : 'inactive', // 90% active
                'category_id' => null,
            ]);
        }
    }
}
