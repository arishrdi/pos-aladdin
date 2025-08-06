<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Kue Kering', 'description' => 'Aneka kue kering lezat'],
            ['name' => 'Roti Manis', 'description' => 'Berbagai macam roti manis'],
            ['name' => 'Kue Basah', 'description' => 'Kue basah tradisional dan modern'],
            ['name' => 'Pastry', 'description' => 'Aneka pastry dan croissant'],
            ['name' => 'Minuman', 'description' => 'Minuman segar dan hangat'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
