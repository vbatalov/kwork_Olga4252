<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Response;
use App\Models\Specialist;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Throwable;


class OrderSpecialistController extends Controller
{
    public SimpleVK $bot;
    public Specialist $specialist;

    public string $action;
    public string $data;
    public string $offset;

    public ButtonsSpecialist $button;
    public Buttons $buttonCostumer;

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
        $this->buttonCostumer = new Buttons();
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
            $this->offer_price(order_id: $this->data);
        }

        if ($this->action == "send_response") {
            $this->send_response(order_id: $this->data);
        }
    }


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

    // Просмотр заказа

    private function view_order($order_id, $offset)
    {
        $order = Order::findOrFail($order_id);
        $text = view("messages_specialist.order_view", compact("order"));

        $this->bot->eventAnswerSnackbar("Просмотр заказа");
        $this->bot->msg($text)->kbd($this->button->view_order($order_id, $offset))->send();
    }

    // Предложить цену
    private function offer_price($order_id)
    {
        $this->specialist->update([
            "cookie" => "offer_price_id_" . $order_id
        ]);

        $this->bot->msg('Отправьте цену за выполнение следующим сообщением.')
            ->kbd($this->button->send_response_price($this->offset))->send();

    }

    private function send_response($order_id)
    {
        Response::setStatus(executor_id: $this->specialist->id, order_id: $order_id, status: "awaits");

        $this->send_notification_to_costumer_about_new_response($order_id);

        $this->bot->reply("remove here. die."); return;

        $this->bot->msg("Предложение направлено заказчику.")
            ->kbd($this->button->ordersOrMainMenu())
            ->send();
    }

    private function send_notification_to_costumer_about_new_response($order_id)
    {
        $order = Order::findOrFail($order_id);
        $user = $order->user;

        $message = view("messages.new_order_response", compact("order"));

        $VKStudentController = new VKStudentController();
        $VKStudentController->bot->msg("$message")
            ->kbd($this->buttonCostumer->startChatWithSpecialist($this->specialist->id), true)
            ->send($user->peer_id);
    }

}
