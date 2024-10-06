<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\Message;
use App\Models\Order;
use App\Models\Specialist;
use App\Models\Subject;
use App\Models\User;
use Faker\Factory;
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
                        "name" => "Аналитическая геометрия",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Высшая математика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Дискретная математика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Доп. главы по математике (excel)",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Мат. анализ",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Мат. логика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Мат. методы",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Линейная алгебра",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Теор. вер. и мат. статистика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Теория функций комплексного переменного (ТФКП)",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Уравнения мат. физики",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Другое",
                        "description" => "Описание предмета 2",
                    ],
                ],
            ],
            [
                "name" => "Естествознание",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Биология",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Геология",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Нанотехнологии",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Физика",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Химия",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Аналитическая химия и ФХМА ",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Квантовая химия",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Коллоидная химия",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "ОНХ (общая и неорганическая химия)",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Органическая химия",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Теор. и эксперим. методы в химии",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Физическая химия",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Кристаллография",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Экология",
                        "description" => "Описание предмета",
                    ],
                    [
                        "name" => "Другое",
                        "description" => "Описание предмета",
                    ],
                ],
            ],
            [
                "name" => "Инженерия",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Аналитическая механика",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "БЖД",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Биотехнология",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Водоочистка и водоподготовка",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Железнодорожный транспорт",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Инж. графика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Комп. моделирование (ХТП)",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Материаловедение",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Металлургия",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Механика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Метрология",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Оптика (+спектры)",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Общая химическая технология (ОХТ)",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Процессы и аппараты в ХТ (ПАХТ + МПАХТ)",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Сварка и сварочное производство",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Соц-эконом",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Английский язык",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Всемирная история",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Журналистика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "История России",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Культурология и искусствоведение",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Менеджмент",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Педагогика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Психология",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Русский язык",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Социология",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Управление проектами (бизнес-планы)  ",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Философия",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Экономика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Другое",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Лингвистика",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "Английский",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "Арабский (фусха, диалекты)",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Испанский",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Русский",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Немецкий",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Французский",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Японский",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Другой язык",
                        "description" => "Описание предмета 2",
                    ]
                ],
            ],
            [
                "name" => "Программирование",
                "description" => "",
                "subjects" => [
                    [
                        "name" => "C",
                        "description" => "Описание предмета 1",
                    ],
                    [
                        "name" => "C#",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "C++",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Информатика",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Питон",
                        "description" => "Описание предмета 2",
                    ],
                    [
                        "name" => "Другое",
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

        User::create([
            "peer_id" => 120637023,
//            "role" => "student"
        ]);

        Specialist::create([
            "peer_id" => 120637023,
//            "role" => "student"
        ]);
        Order::create([
            "user_id" => "1",
            "category_id" => "1",
            "subject_id" => "1",
            "need_help_with" => "Диплом",
            "description" => "",
            "deadline" => "2-3 дня",
            "status" => "pending",
            "executor_id" => "",
            "completion_date" => "",
        ]);

        Message::create([
            "from" => 1,
            "sender" => "user",
            "to" => 2,
            "order_id" => 1,
            "recipient" => "specialist",
            "message" => Factory::create()->text()
        ]);
//        sleep(1);
        Message::create([
            "from" => 1,
            "sender" => "specialist",
            "to" => 2,
            "order_id" => 1,
            "recipient" => "user",
            "message" => Factory::create()->text()
        ]);


    }
}
