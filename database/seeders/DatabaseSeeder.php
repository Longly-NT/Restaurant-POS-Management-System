<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\DiningTable;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

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
            [$starters, 'Spring Rolls', 4.50, 'spring-rolls.jpg'],
            [$starters, 'Garlic Bread', 3.50, 'garlic-bread.jpg'],
            [$mains, 'Grilled Chicken', 12.00, 'grilled-chicken.jpg'],
            [$mains, 'Beef Burger', 10.50, 'beef-burger.jpg'],
            [$mains, 'Margherita Pizza', 9.50, 'margherita-pizza.jpg'],
            [$drinks, 'Coca-Cola', 2.00, 'coca-cola.jpg'],
            [$drinks, 'Orange Juice', 2.50, 'orange-juice.jpg'],
            [$drinks, 'Mojito', 6.00, 'mojito.jpg'],
            [$desserts, 'Chocolate Cake', 5.00, 'chocolate-cake.jpg'],
            [$desserts, 'Ice Cream', 3.00, 'ice-cream.jpg'],
        ];

        foreach ($items as [$category, $name, $price, $imageFile]) {
            $imagePath = null;
            $sourcePath = database_path('seeders/images/'.$imageFile);

            if (file_exists($sourcePath)) {
                $storedName = uniqid().'_'.$imageFile;
                Storage::disk('public')->put('menu-items/'.$storedName, file_get_contents($sourcePath));
                $imagePath = 'menu-items/'.$storedName;
            }

            MenuItem::create([
                'category_id' => $category->id,
                'name' => $name,
                'price' => $price,
                'image' => $imagePath,
                'is_available' => true,
            ]);
        }
    }
}