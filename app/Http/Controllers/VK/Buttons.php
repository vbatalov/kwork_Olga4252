<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subject;
use Carbon\Carbon;
use Carbon\Traits\Date;

class Buttons extends Controller
{
    public VKController $vk;

    public function __construct()
    {
        $this->vk = new VKController();
    }

    /** Главное меню */
    public function mainMenu()
    {
        $items = [
            [
                "text" => "Новый заказ",
                "color" => "white",

                "is_mainMenu" => true,
                "data" => "new_order",

            ],
            [
                "text" => "Мои заказы",
                "color" => "white",

                "is_mainMenu" => true,
                "data" => "my_orders",

            ],
            [
                "text" => "Избранные специалисты",
                "color" => "white",

                "is_mainMenu" => true,
                "data" => "my_favorites_specialists",

            ],
            [
                "text" => "Настроить личный кабинет",
                "color" => "white",
                "is_mainMenu" => true,
                "data" => "user_profile_setting",

            ],
        ];

        /** Массив кнопок главного меню */
        $buttons = [];
        foreach ($items as $item) {
            $buttons [] = [
                $this->vk->bot->buttonCallback($item['text'], $item['color'], [
                    "is_mainMenu" => $item['is_mainMenu'],
                    "data" => $item['data'],
                ])
            ];
        }

        return $buttons;
    }

    /** Меню категорий */
    public function categories()
    {
        $categories = Category::all();
        $buttons = [];

        foreach ($categories as $category) {
            $buttons[] = $this->vk->bot->buttonCallback($category->name, 'white', [
                'is_category' => true,
                'data' => "$category->id"
            ]);
        }

        $buttons [] = $this->mainMenuButton();
        return array_chunk($buttons, 2);
    }

    /** Меню предметов в категории */
    public function subjects(int $id)
    {
        $items = Subject::where("category_id", $id)->get()->all();
        $buttons = [];

        foreach ($items as $item) {
            $name_filter = mb_strimwidth($item->name, 0, 40);
            $buttons[] = $this->vk->bot->buttonCallback($name_filter, 'white', [
                'is_subject' => true,
                'data' => "$item->id"
            ]);
        }

        $buttons [] = $this->mainMenuButton();
        return array_chunk($buttons, 2);
    }

    /** Меню выбора: с чем нужно помочь */
    public function whatYouNeedHelpWith()
    {
        $items = [
            "Онлайн работа",
            "Курсовая",
            "Реферат / лит. обзор",
            "Репетиторство",
            "Диплом",
            "Другое",
        ];
        $buttons = [];

        foreach ($items as $item) {
            $buttons[] = $this->vk->bot->buttonCallback($item, 'white', [
                'is_whatYouNeedHelpWith' => true,
                'data' => $item,
            ]);
        }

        $buttons [] = $this->mainMenuButton();
        return array_chunk($buttons, 2);
    }

    /** Кнопка: сроки */
    public function deadlines()
    {
        $items = [
            [
                "title" => "Срочно",
                "data" => Carbon::now(),
            ],
            [
                "title" => "1 день",
                "data" => Carbon::now()->add("1 day"),
            ],
            [
                "title" => "2-3 дня",
                "data" => Carbon::now()->add("3 day"),
            ],
            [
                "title" => "4-7 дней",
                "data" => Carbon::now()->add("7 day"),
            ],
            [
                "title" => "Более недели",
                "data" => "Более недели",
            ],
        ];
        $buttons = [];

        foreach ($items as $item) {
            $buttons[] = $this->vk->bot->buttonCallback($item['title'], 'white', [
                'is_deadline' => true,
                'data' => $item['data'],
            ]);
        }

        $buttons [] = $this->mainMenuButton();
        return array_chunk($buttons, 2);
    }


    /** Кнопка главное меню */
    private function mainMenuButton()
    {
        return $this->vk->bot->buttonCallback("Главное меню", 'blue', [
            'data' => "menu"
        ]);
    }
}
