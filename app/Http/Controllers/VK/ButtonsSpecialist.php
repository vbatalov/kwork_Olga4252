<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Collection;

class ButtonsSpecialist extends Controller
{
    public VKStudentController $vk;

    public function __construct()
    {
        $this->vk = new VKStudentController();
    }

    /** Главное меню */
    public function mainMenu()
    {
        $items = [
            [
                "text" => "Доступные заказы",
                "color" => "green",
                "action" => "orders_available",
                "data" => 0
            ],
            [
                "text" => "Заказы в работе",
                "color" => "blue",
                "action" => "my_orders",

            ],
            [
                "text" => "Баланс",
                "color" => "blue",
                "action" => "balance",

            ],
        ];

        /** Массив кнопок главного меню */
        $buttons = [];
        foreach ($items as $item) {
            $buttons [] = [
                $this->vk->bot->buttonCallback($item['text'], $item['color'], [
                    "action" => $item['action'],
                    "data" => $item['data'] ?? null,
                ])
            ];
        }

        return $buttons;
    }

    /** Список доступных заказов */
    public function orders_available(Collection $orders, $offset)
    {
        $buttons = [];
        foreach ($orders as $order) {

            $buttons[] = $this->vk->bot->buttonCallback(text: "Заказ № $order->id",
                color: 'green', payload: [
                    "action" => "view_order",
                    'data' => $order->id,
                    'offset' => $offset
                ]);

        }

        $chunk = array_chunk($buttons, 2);

        $next_page = $this->vk->bot->buttonCallback(text: "Следующая страница",
            color: 'blue', payload: [
                "action" => "orders_available",
                'data' => $offset + 5
            ]);


        if ($offset >= 5) {
            $prev_page = $this->vk->bot->buttonCallback(text: "Предыдущая страница",
                color: 'blue', payload: [
                    "action" => "orders_available",
                    'data' => $offset - 5
                ]);

            $chunk [] = [$prev_page, $next_page];
        } else {
            $chunk [] = [$next_page];
        }


        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    /** Кнопка главное меню */
    public
    function mainMenuButton()
    {
        return $this->vk->bot->buttonCallback("Главное меню", 'white', [
            'data' => "menu",
            'action' => "return_to_home"
        ]);
    }

    public function view_order($order_id, $offset)
    {
        $buttons = [];
        $chunk = array_chunk($buttons, 2);

        $offer_price = $this->vk->bot->buttonCallback(text: "Предложить цену",
            color: 'green', payload: [
                "action" => "offer_price",
                'data' => $order_id,
                'offset' => $offset
            ]);

        $paginate = $this->vk->bot->buttonCallback(text: "Назад",
            color: 'blue', payload: [
                "action" => "orders_available",
                'data' => $offset
            ]);


        $chunk [] = [$offer_price];
        $chunk [] = [$paginate];


        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    public function send_response_price($offset)
    {
        $buttons = [];
        $chunk = array_chunk($buttons, 2);


        $paginate = $this->vk->bot->buttonCallback(text: "Назад",
            color: 'blue', payload: [
                "action" => "orders_available",
                'data' => $offset
            ]);


        $chunk [] = [$paginate];


        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    public function send_response($order_id)
    {
        $buttons = [];
        $chunk = array_chunk($buttons, 2);


        $send_response = $this->vk->bot->buttonCallback(text: "Отправить предложение",
            color: 'green', payload: [
                "action" => "send_response",
                'data' => $order_id
            ]);

        $paginate = $this->vk->bot->buttonCallback(text: "Вернуться к списку заказов",
            color: 'blue', payload: [
                "action" => "orders_available",
                'data' => 0
            ]);


        $chunk [] = [$send_response];
        $chunk [] = [$paginate];


        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    public function ordersOrMainMenu()
    {
        $buttons = [];
        $chunk = array_chunk($buttons, 2);


        $paginate = $this->vk->bot->buttonCallback(text: "Вернуться к списку заказов",
            color: 'blue', payload: [
                "action" => "orders_available",
                'data' => 0
            ]);

        $chunk [] = [$paginate];


        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
    }

    public function goBack($action, $text = "Назад")
    {
        return $this->vk->bot->buttonCallback("$text", 'white', [
            'action' => "$action"
        ]);
    }
}
