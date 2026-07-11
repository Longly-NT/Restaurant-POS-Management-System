<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@pos.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Sam Staff',
            'email' => 'staff@pos.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        User::create([
            'name' => 'Charlie Chef',
            'email' => 'chef@pos.test',
            'password' => Hash::make('password'),
            'role' => 'chef',
        ]);

        User::create([
            'name' => 'Manager Kim',
            'email' => 'manager@pos.test',
            'password' => Hash::make('password'),
            'role' => 'manager',
        ]);

        foreach (range(1, 8) as $i) {
            DiningTable::create([
                'name' => 'Table '.$i,
                'capacity' => $i % 2 === 0 ? 4 : 2,
            ]);
        }

        $starters = Category::create(['name' => 'Starters', 'station' => 'kitchen']);
        $mains = Category::create(['name' => 'Main Course', 'station' => 'kitchen']);
        $drinks = Category::create(['name' => 'Drinks', 'station' => 'bar']);
        $desserts = Category::create(['name' => 'Desserts', 'station' => 'kitchen']);

        $items = [
            [$starters, 'Spring Rolls', 4.50],
            [$starters, 'Garlic Bread', 3.50],
            [$mains, 'Grilled Chicken', 12.00],
            [$mains, 'Beef Burger', 10.50],
            [$mains, 'Margherita Pizza', 9.50],
            [$drinks, 'Coca-Cola', 2.00],
            [$drinks, 'Orange Juice', 2.50],
            [$drinks, 'Mojito', 6.00],
            [$desserts, 'Chocolate Cake', 5.00],
            [$desserts, 'Ice Cream', 3.00],
        ];

        foreach ($items as [$category, $name, $price]) {
            MenuItem::create([
                'category_id' => $category->id,
                'name' => $name,
                'price' => $price,
                'is_available' => true,
            ]);
        }
    }
}
