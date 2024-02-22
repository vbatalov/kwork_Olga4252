<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;

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
                "text" => "Опубликовать",
                "color" => "green",
                "data" => "new_order",

            ],
            [
                "text" => "Мои заказы",
                "color" => "blue",
                "data" => "my_orders",

            ],
            [
                "text" => "Избранные специалисты",
                "color" => "blue",
                "data" => "my_favorites_specialists",

            ],
            [
                "text" => "Настроить личный кабинет",
                "color" => "blue",
                "data" => "user_profile_setting",

            ],
        ];

        /** Массив кнопок главного меню */
        $buttons = [];
        foreach ($items as $item) {
            $buttons [] = [
                $this->vk->bot->buttonCallback($item['text'], $item['color'], [
                    "action" => $item['data'],
//                    "data" => null,
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
            $buttons[] = $this->vk->bot->buttonCallback($category->name, 'blue', [
                "action" => "newOrderSaveCategoryAndShowSubject",
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
            $buttons[] = $this->vk->bot->buttonCallback($name_filter, 'blue', [
                'action' => "newOrderSaveSubjectAndShowWhatYouNeedHelpWith",
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
            $buttons[] = $this->vk->bot->buttonCallback($item, 'blue', [
                'action' => "newOrderSaveWhatYouNeedHelpWithAndShowDeadlines",
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
                "data" => "1 день",
            ],
            [
                "title" => "2-3 дня",
                "data" => "2-3 дня",
            ],
            [
                "title" => "4-7 дней",
                "data" => "4-7 дней",
            ],
            [
                "title" => "Более недели",
                "data" => "Более недели",
            ],
        ];
        $buttons = [];

        foreach ($items as $item) {
            $buttons[] = $this->vk->bot->buttonCallback($item['title'], 'blue', [
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
        $buttons[] = $this->vk->bot->buttonCallback("Опубликовать заказ", 'green', [
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

            $buttons[] = $this->vk->bot->buttonCallback("Заказ № $order->id ($category_name)", 'blue', [
                'action' => "view_order",
                'data' => "$order->id",
            ]);
        }

        $chunk = array_chunk($buttons, 2);
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }


    public function boostSearchSpecialist(Order $order)
    {

        $buttons = [];

        $buttons[] = $this->vk->bot->buttonCallback("Оплатить на карту Сбербанк", "blue", [
            'action' => "payBoostSearchSpecialist_bySberbank",
            'data' => $order->id,
        ]);

        $buttons[] = $this->vk->bot->buttonCallback("Оплатить с YooКасса", "blue", [
            'action' => "payBoostSearchSpecialist_byYouKassa",
            'data' => $order->id,
        ]);

        $chunk = array_chunk($buttons, 1);
        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    /** Действия с заказом */
    public function orderActions(Order $order)
    {

        $items =
            [
                [
                    "text" => 'Ускорить поиск исполнителя',
                    "action" => 'boost_search_specialist',
                    "color" => "green",
                    "data" => $order->id,
                ],
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
//        $chunk [] = [$this->goBack("my_orders")]; // TODO: не работает, т.к. payload с главного меню click_from_main_menu, а затем my_orders
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
        return $this->vk->bot->buttonCallback("Главное меню", 'white', [
            'data' => "menu",
            'action' => "return_to_home"
        ]);
    }
}
