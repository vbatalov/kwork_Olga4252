<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Pagination\LengthAwarePaginator;

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
                "text" => "Взять заказ",
                "color" => "green",
                "action" => "orders_available",
                "data" => 1
            ],
            [
                "text" => "Заказы в работе",
                "color" => "green",
                "action" => "my_orders",

            ],
//            [
//                "text" => "Личный кабинет",
//                "color" => "white",
//                "action" => "account",
//
//            ],
        ];

        /** Массив кнопок главного меню */
        $buttons = [];
        foreach ($items as $item) {
            $buttons [] =
                $this->vk->bot->buttonCallback($item['text'], $item['color'], [
                    "action" => $item['action'],
                    "data" => $item['data'] ?? null,
                ]);

        }

        return array_chunk($buttons, 2);
    }

    /** Список доступных заказов */
    public function orders_available(LengthAwarePaginator $orders, $page)
    {
        $buttons = [];
        foreach ($orders as $order) {

            $buttons[] = $this->vk->bot->buttonCallback(text: "Заказ № $order->id",
                color: 'green', payload: [
                    "action" => "view_order",
                    'data' => $order->id,
                    'offset' => $page
                ]);

        }

        $chunk = array_chunk($buttons, 2);

        $next_page = [];
        $prev_page = [];
        if ($orders->hasMorePages()) {
            $next_page[] = $this->vk->bot->buttonCallback(text: ">",
                color: 'blue', payload: [
                    "action" => "orders_available",
                    'data' => $page + 1
                ]);
        }


        if (!$orders->onFirstPage()) {
            $prev_page[] = $this->vk->bot->buttonCallback(text: "<",
                color: 'blue', payload: [
                    "action" => "orders_available",
                    'data' => $page - 1
                ]);
        }

        $chunk [] = array_merge($prev_page, $next_page);

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

    public function my_orders($orders)
    {
        $buttons = [];

        /** @var Order $order */
        foreach ($orders as $order) {
            $buttons[] = $this->vk->bot->buttonCallback(text: "Заказ № $order->id",
                color: 'green', payload: [
                    "action" => "view_my_order",
                    'data' => $order->id,
                ]);
        }

        $chunk = array_chunk($buttons, 2);

        $chunk [] = [$this->mainMenuButton()];
        return $chunk;
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

    public function view_my_order($order_id)
    {
        $order = Order::findOrFail($order_id);

        $buttons[] = $this->vk->bot->buttonCallback(text: "Сдать работу",
            color: 'green', payload: [
                "action" => "submit_work",
                'data' => $order_id,
            ]);

        $buttons[] = $this->vk->bot->buttonCallback(text: "Начать чат",
            color: 'green', payload: [
                "action" => "chat_with",
                "data" => $order_id,
                "chat_with" => $order->user_id
            ]);

        $buttons[] = $this->vk->bot->buttonCallback(text: "Назад",
            color: 'blue', payload: [
                "action" => "my_orders",
            ]);

        $buttons = array_chunk($buttons, 2);

        $buttons[] = [$this->mainMenuButton()];

        return $buttons;
    }

    public function submit_work()
    {
        $buttons[] = $this->vk->bot->buttonCallback(text: "Назад",
            color: 'blue', payload: [
                "action" => "my_orders",
            ]);

        $buttons = array_chunk($buttons, 2);

        $buttons[] = [$this->mainMenuButton()];

        return $buttons;
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

    public function start_chat_with($user_id, $order_id, $buttonAlert = false)
    {
        $order = Order::findOrFail($order_id);

        $changeResponseButton = $this->change_response($order_id);
        if (!$buttonAlert) {
            if ($order->status == 'pending') {
                return
                    [
                        [$changeResponseButton],
                    ];
            }
        }

        $buttons = [];
        $buttons[] = [$this->button_start_chat($user_id, $order_id)];
        if ($order->status == 'pending') {
            $buttons[] = [$changeResponseButton];
        }
        return $buttons;
    }

    private function cancel_response($order_id)
    {
        return $this->vk->bot->buttonCallback(text: "Отменить предложение",
            color: 'red', payload: [
                "action" => "cancel_response",
                "data" => $order_id,
            ]);
    }

    public function button_start_chat($user_id, $order_id)
    {
        return $this->vk->bot->buttonCallback(text: "Начать чат",
            color: 'green', payload: [
                "action" => "chat_with",
                "data" => $order_id,
                "chat_with" => $user_id
            ]);
    }

    public function confirmSubmitWork($order_id)
    {
        return $this->vk->bot->buttonCallback(text: "Подтвердить выполнение заказа",
            color: 'green', payload: [
                "action" => "confirm_submit_order",
                "data" => $order_id,
            ]);
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

    private function change_response($order_id)
    {
        return $this->vk->bot->buttonCallback(text: "Изменить предложение",
            color: 'white', payload: [
                "action" => "offer_price",
                "data" => $order_id,
            ]);
    }
}
