<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Order;
use App\Models\Specialist;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Illuminate\Support\Facades\Http;
use Throwable;


class OrderSpecialistController extends Controller
{
    public SimpleVK $bot;
    public Specialist $specialist;

    public string $action;
    public string $data;
    public string $offset;

    public ButtonsSpecialist $button;

    public Order $order;

    public function __construct(SimpleVK $bot, Specialist $specialist, array $payload)
    {
        $this->bot = $bot;
        $this->specialist = $specialist;

        $this->action = $payload['action'] ?? "null";
        $this->data = $payload['data'] ?? "null";
        $this->offset = $payload['offset'] ?? "null";

        $this->order = new Order();
        $this->button = new ButtonsSpecialist();
    }

    /**
     * @throws SimpleVkException|Throwable
     */
    public function init(): void
    {
        // Управление логикой при просмотре заказов
        $this->OrderLogic();
    }

    /**
     * @throws SimpleVkException
     */
    // Доступные заказы
    private function orders_available($offset = 0)
    {
        $orders = $this->order->getAvailableOrders(offset: $offset);

        // Устанавливаю куки пользователю, пока он выбирает заказ и от него не требуется ввода сообщений
        $this->specialist->update([
            "cookie" => "orders_available"
        ]);

        $message = "Доступные заказы";
        $this->bot->eventAnswerSnackbar("$message");

        $text = view("messages_specialist.orders_available", compact("orders"));
        $this->bot->msg("$text")->kbd($this->button->orders_available($orders, $offset))->send();
    }


    /**
     * @throws SimpleVkException
     * @throws Throwable
     */
    private function OrderLogic(): void
    {
        // Нажата кнопка Новый заказ
        if ($this->action == "orders_available") {
            $this->orders_available(offset: $this->data);
        }

        if ($this->action == "view_order") {
            $this->view_order(order_id: $this->data, offset: $this->offset);
        }

        if ($this->action == "offer_price") {
            $this->offer_price();
        }
    }

    // Просмотр заказа
    private function view_order($order_id, $offset)
    {
        $order = Order::findOrFail($order_id);
        $text = view("messages_specialist.order_view", compact("order"));

        $this->bot->eventAnswerSnackbar("Просмотр заказа");
        $this->bot->msg($text)->kbd($this->button->view_order($order_id, $offset))->send();
    }

    // Предложить цену
    private function offer_price()
    {
    }

}
