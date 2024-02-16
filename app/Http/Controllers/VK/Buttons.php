<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Category;

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
                'data' => "$category->name"
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
