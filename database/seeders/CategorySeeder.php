<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Chairs', 'description' => 'Office chairs, dining chairs, and seating solutions'],
            ['name' => 'Tables', 'description' => 'Dining tables, coffee tables, and work desks'],
            ['name' => 'Sofas', 'description' => 'Comfortable sofas and living room seating'],
            ['name' => 'Beds', 'description' => 'Beds and bedroom furniture'],
            ['name' => 'Storage', 'description' => 'Cabinets, shelves, and storage solutions'],
            ['name' => 'Lighting', 'description' => 'Lamps, chandeliers, and lighting fixtures']
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description']]
            );
        }
    }
}
