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
    public string $chat_with;

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
        $this->chat_with = $payload['chat_with'] ?? "null";

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
            $this->orders_available(page: $this->data);
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

        if ($this->action == "chat_with") {
            $this->start_chat_with_user();
        }

        if ($this->action == "cancel_response") {
            $this->cancel_response();
        }
    }


    private function orders_available($page = 1)
    {
        $availableCategoriesSpecialist = [];
        foreach ($this->specialist->categories as $category) {
            $availableCategoriesSpecialist[] = $category->subject_id;
        }
        $orders = $this->order->getAvailableOrders(page: $page, categories: $availableCategoriesSpecialist);

        // Устанавливаю куки пользователю, пока он выбирает заказ и от него не требуется ввода сообщений
        $this->specialist->update([
            "cookie" => "orders_available"
        ]);

        $message = "Доступные заказы";
        $this->bot->eventAnswerSnackbar("$message");

        $text = view("messages_specialist.orders_available", compact("orders"));
        $this->bot->msg("$text")->kbd($this->button->orders_available($orders, $page))->send();
    }

    // Просмотр заказа

    private function view_order($order_id, $offset)
    {
        $order = Order::findOrFail($order_id);
        $text = view("messages_specialist.order_view", compact("order"));

        $this->bot->eventAnswerSnackbar("Просмотр заказа");
        $this->bot->msg($text)->kbd($this->button->view_order($order_id, $offset))->send();

        $attachments = $order->attachments;
        if ($attachments->count()) {
            $this->bot->reply("Количество вложений в заказе: {$attachments->count()}");

            foreach ($attachments as $attachment) {
                $url = $attachment->attachments['url'];

                $this->bot->reply("$attachment->message\n" . asset("storage/$url"));
            }
        }
    }

    // Предложить цену
    private function offer_price($order_id)
    {
        $this->specialist->update([
            "cookie" => "offer_price_id_" . $order_id
        ]);

        $this->bot->eventAnswerSnackbar("Укажите цену");
        $this->bot->msg('Отправьте цену за выполнение следующим сообщением.')
            ->kbd($this->button->send_response_price($this->offset))->send();

    }

    private function send_response($order_id)
    {
        Response::setStatus(order_id: $order_id, status: "awaits");

        $this->send_notification_to_costumer_about_new_response($order_id);
//        return;

        $this->bot->eventAnswerSnackbar("Предложение направлено");
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
            ->kbd($this->buttonCostumer->start_chat_with($this->specialist->id, $order_id, true), true)
            ->send($user->peer_id);
    }

    /**
     * @return void
     */
    public function start_chat_with_user(): void
    {
        $this->bot->eventAnswerSnackbar("Отправьте сообщение");
        $this->bot->reply("Вы начали чат с заказчиком. Отправьте сообщение.");
        $this->specialist->update([
            "cookie" => "chat_with_" . $this->chat_with . "|" . "order_id_" . $this->data,
        ]);
    }

    private function cancel_response()
    {
        $cookie = "cancel_response_" . $this->data;
        $this->specialist->update([
            "cookie" => $cookie
        ]);

        $this->bot->eventAnswerSnackbar("Для отмены предложения следуйте инструкции");
        $this->bot->reply("Если вы хотите удалить предложение, напишите сообщение: 'Удалить отклик {$this->data}'");
    }

}
