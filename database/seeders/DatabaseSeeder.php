<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** Список категорий */
        $categories = [
            [
                "name" => "Математика",
                "description" => "",
            ],
            [
                "name" => "Естествознание",
                "description" => "",
            ],
            [
                "name" => "Инженерия",
                "description" => "",
            ],
            [
                "name" => "Соц-эконом",
                "description" => "",
            ],
            [
                "name" => "Лингвистика",
                "description" => "",
            ],
            [
                "name" => "Программирование",
                "description" => "",
            ],
        ];
        foreach ($categories as $item) {
            Category::create([
                "name" => $item['name'],
                "description" => $item['description']
            ]);
        }
    }
}
