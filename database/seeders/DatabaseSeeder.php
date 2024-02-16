<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /** Список категорий и предметов */
        $categories = [
            [
                "name" => "Математика",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Предмет 1",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Предмет 2",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Естествознание",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Предмет 1",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Предмет 2",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Инженерия",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Предмет 1",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Предмет 2",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Соц-эконом",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Предмет 1",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Предмет 2",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Лингвистика",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Предмет 1",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Предмет 2",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Программирование",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Предмет 1",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Предмет 2",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
        ];
        foreach ($categories as $item) {
            /** Создание категорий */
            $category = Category::create([
                "name" => $item['name'],
                "description" => $item['description']
            ]);

            /** Создание предметов к категории */
            foreach ($item["subjects"] as $subject) {
                Subject::create([
                    "name" => $subject["name"],
                    "description" => $subject['description'],
                    "category_id" => $category->id,
                ]);
            }
        }
    }
}
