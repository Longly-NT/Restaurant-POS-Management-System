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
    // Starters
    [$starters, "A-ping", 2.50, 'A-ping.jpg'],
    [$starters, "Bok L'hong", 3.50, "Bok L'hong.jpg"],
    [$starters, 'Chien Chuon', 3.00, 'Chien chuon.jpg'],
    [$starters, 'Chrouk Svay', 3.00, 'Chrouk Svay.jpg'],
    [$starters, 'Lap Khmer', 5.50, 'Lap Khmer.jpg'],
    [$starters, 'Plea Sach Ko', 5.00, 'Plea sach ko.jpg'],
    [$starters, 'Ngam Ngov', 3.50, 'ngam ngov.jpg'],

    // Main Course
    [$mains, 'Borbor', 3.50, 'Borbor.jpg'],
    [$mains, 'Char Kdaw', 6.00, 'Char Kdaw.jpg'],
    [$mains, 'Kampot Pepper Crab', 14.00, 'Kampot Pepper Crab.jpg'],
    [$mains, 'Kuy Teav', 4.50, 'Kuy Teav.jpg'],
    [$mains, 'Lok Lak', 8.50, 'Lok Lak.jpg'],
    [$mains, 'Nom Banh Chok', 4.00, 'Nom Banh Chok.jpg'],
    [$mains, 'Samlar Machu', 5.00, 'Samlar Machu.jpg'],
    [$mains, 'Samlor Kako', 5.50, 'Samlor Kako.jpg'],
    [$mains, "Somlar Kari", 6.00, 'Somlar Kari.jpg'],
    [$mains, 'Somlar Machu', 5.00, 'Somlar Machu.jpg'],
    [$mains, 'Amok', 7.50, 'Traditional-Cambodian-Dishes-To-Eat-Amok-1024x594.jpg'],
    [$mains, 'Yaohon', 15.00, 'Yaohon.jpg'],
    [$mains, 'Bai Sach Chrouk', 3.50, 'bai sach chrouk.jpg'],
    [$mains, 'Meet Ketang', 2.00, 'Meet ketang.jpg'],

    // Drinks
    [$drinks, 'Tek Ombao', 1.50, 'Tek Ombao (Sugarcane Juice).jpg'],
    [$drinks, 'Tikalok', 2.00, 'Tikalok.jpg'],
    [$drinks, 'Kafe Tuk Dos Ko', 1.50, 'Kafe Tuk Dos Ko.jpg'],

    // Desserts
    [$desserts, 'Ansom Chek', 2.00, 'Ansom Chek.jpg'],
    [$desserts, "Banh J'neurk", 2.00, "Banh J'neurk.jpg"],
    [$desserts, 'Nhom Play Ai', 2.50, 'Nhom Play Ai.jpg'],
    [$desserts, 'Nom Akor', 2.00, 'Nom Akor.jpg'],
    [$desserts, 'Nom Korng', 2.00, 'Nom Korng.jpg'],
    [$desserts, 'Nom Lort', 2.00, 'Nom Lort.jpg'],
    [$desserts, 'Num Chak Kachan', 2.50, 'Num Chak Kachan.jpg'],
    [$desserts, 'Sangkya Lpov', 2.50, 'sangkya lpov.jpg'],
    [$desserts, 'Cha Houy Teuk', 2.00, 'Cha Houy Teuk.jpg'],
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