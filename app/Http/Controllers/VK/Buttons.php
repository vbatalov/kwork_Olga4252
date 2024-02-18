<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Subject;
use App\Models\User;
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
                "data" => "new_order",

            ],
            [
                "text" => "Мои заказы",
                "color" => "white",
                "data" => "my_orders",

            ],
            [
                "text" => "Избранные специалисты",
                "color" => "white",
                "data" => "my_favorites_specialists",

            ],
            [
                "text" => "Настроить личный кабинет",
                "color" => "white",
                "data" => "user_profile_setting",

            ],
        ];

        /** Массив кнопок главного меню */
        $buttons = [];
        foreach ($items as $item) {
            $buttons [] = [
                $this->vk->bot->buttonCallback($item['text'], $item['color'], [
                    "action" => "click_from_main_menu",
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
                "action" => "click_from_category",
                'data' => "$category->id"
            ]);
        }

        $chunk = array_chunk($buttons, 2);
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    /** Меню предметов в категории */
    public function subjects(int $id)
    {
        $items = Subject::where("category_id", $id)->get()->all();
        $buttons = [];

        foreach ($items as $item) {
            $name_filter = mb_strimwidth($item->name, 0, 40);
            $buttons[] = $this->vk->bot->buttonCallback($name_filter, 'white', [
                'action' => "click_from_subject",
                'data' => "$item->id"
            ]);
        }

        $chunk = array_chunk($buttons, 2);
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
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
                'action' => "click_from_is_whatYouNeedHelpWith",
                'data' => $item,
            ]);
        }


        $chunk = array_chunk($buttons, 2);
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    /** Кнопка: сроки */
    public function deadlines()
    {
        $items = [
            [
                "title" => "Срочно",
                "data" => "Срочно",
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
                'action' => "click_from_deadline",
                'data' => $item['data'],
            ]);
        }

        $chunk = array_chunk($buttons, 2);
        $chunk [] = [$this->goBack("click_from_subject")];
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    /** Опубликовать заявку */
    public function publishOrder()
    {
        $buttons[] = $this->vk->bot->buttonCallback("Опубликовать заказ", 'white', [
            'action' => "publish_order",
            'data' => "publish_order",
        ]);

        $chunk = array_chunk($buttons, 2);
        $chunk [] = [$this->goBack("click_from_is_whatYouNeedHelpWith")];
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    /** Просмотр заказов пользователя */
    public function getOrdersByUser(User $user)
    {
        $orders = Order::where("user_id", $user->id)->get()->all();
        $buttons = [];
        foreach ($orders as $order) {
            $category_name = $order->category->name; // Наименование категории заказа

            $buttons[] = $this->vk->bot->buttonCallback("Заказ № $order->id ($category_name)", 'white', [
                'action' => "view_order",
                'data' => "$order->id",
            ]);
        }

        $chunk = array_chunk($buttons, 2);
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    /** Действия с заказом */
    public function orderActions(Order $order)
    {

        $items =
            [
//                [
//                    "text" => 'Редактировать заказ',
//                    "action" => 'edit_order',
//                    "color" => "white",
//                    "data" => $order->id,
//                ],
                [
                    "text" => 'Удалить заказ',
                    "color" => "red",
                    "action" => 'delete_order',
                    "data" => $order->id,
                ],
            ];

        $buttons = [];
        foreach ($items as $item) {
            $buttons[] = $this->vk->bot->buttonCallback($item['text'], $item['color'], [
                'action' => $item['action'],
                'data' => $item['data'],
            ]);
        }

        $chunk = array_chunk($buttons, 1);
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    public function sendPayment(User $user)
    {
        return $this->vk->bot->buttonDonateToGroup("-224716757", [
            "user" => $user->id,
        ]);
    }


    public function goBack($action, $text = "Назад")
    {
        return $this->vk->bot->buttonCallback("$text", 'white', [
            'action' => "$action"
        ]);
    }
    /** Кнопка главное меню */
    public function mainMenuButton()
    {
        return $this->vk->bot->buttonCallback("Главное меню", 'blue', [
            'data' => "menu",
            'action' => "return_to_home"
        ]);
    }
}
