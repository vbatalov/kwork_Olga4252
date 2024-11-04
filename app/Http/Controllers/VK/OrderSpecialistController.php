<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Order;
use App\Models\Response;
use App\Models\Specialist;
use App\Models\User;
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

        if ($this->action == "view_my_order") {
            $this->view_my_order(order_id: $this->data);
        }

        if ($this->action == "submit_work") {
            $this->submit_work(order_id: $this->data);
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

        if ($this->action == 'my_orders') {
            $this->my_orders();
        }

        if ($this->action == 'confirm_submit_order') {
            $this->confirm_submit_order($this->data);
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

        $attachments = Attachment::where([
            'order_id' => $order_id,
            'from' => 'student'
        ])->get();

        if ($attachments->count()) {
            $this->bot->reply("Количество вложений в заказе: {$attachments->count()}");

            foreach ($attachments as $attachment) {
                $url = $attachment->attachments['url'];

                $this->bot->reply("$attachment->message\n" . asset("storage/$url"));
            }
        } else {
            $this->bot->reply("Вложений в заказе нет");
        }
    }


    private function view_my_order($order_id)
    {
        $order = Order::findOrFail($order_id);
        $text = view("messages_specialist.order_view", compact("order"));

        $this->bot->eventAnswerSnackbar("Просмотр заказа");
        $this->bot->msg($text)->kbd($this->button->view_my_order($order_id))->send();

        $attachments = Attachment::where(
            [
                'order_id' => $order->id,
                'from' => 'student'
            ]
        )->get();

        if ($attachments->count()) {
            $this->bot->reply("Количество вложений в заказе: {$attachments->count()}");

            foreach ($attachments as $attachment) {
                $url = $attachment->attachments['url'];

                $this->bot->reply("$attachment->message\n" . asset("storage/$url"));
            }
        }
    }

    private function submit_work($order_id)
    {
        // Устанавливаю куки пользователю, пока он выбирает заказ и от него не требуется ввода сообщений
        $this->specialist->update([
            "cookie" => "submit_work_$order_id"
        ]);

        $message = "Отправить работу";
        $this->bot->eventAnswerSnackbar("$message");

        $text = "Отправьте решение работы следующим сообщением.";
        $this->bot->msg("$text")->kbd($this->button->submit_work())->send();
    }

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
        $is_new = true;

        if (Response::where([
            'order_id' => $order_id,
            'executor_id' => $this->specialist->id,
        ])->count() > 0) {
            $is_new = false;
        }
        Response::setStatus(order_id: $order_id, executor_id: $this->specialist->id, status: "awaits");

        $this->send_notification_to_costumer_about_new_response($order_id, $this->specialist, $is_new);
//        return;

        $this->bot->eventAnswerSnackbar("Предложение направлено");
        $this->bot->msg("Предложение направлено заказчику.")
            ->kbd($this->button->ordersOrMainMenu())
            ->send();
    }

    private function send_notification_to_costumer_about_new_response($order_id, Specialist $specialist, $is_new = true)
    {
        $order = Order::findOrFail($order_id);
        $user = $order->user;

        $message = view("messages.new_order_response", [
            'order' => $order,
            'specialist' => $specialist,
        ]);

        $response = Response::where(['executor_id' => $specialist->id, 'order_id' => $order_id])->latest()->first();

        $VKStudentController = new VKStudentController();
        $VKStudentController->bot->msg("Исполнитель отменил предыдущий отклик.")->send($user->peer_id);

        $VKStudentController->bot->msg("$message")
            ->kbd([
                [$this->buttonCostumer->start_chat_with($this->specialist->id, $order_id, true)],
                [$this->buttonCostumer->accept_offer($order_id, $response->id)]
            ], true)
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

    private function my_orders()
    {
        // Устанавливаю куки пользователю, пока он выбирает заказ и от него не требуется ввода сообщений
        $this->specialist->update([
            "cookie" => "my_orders"
        ]);

        $message = "Заказы в работе";
        $this->bot->eventAnswerSnackbar("$message");

        $orders = Order::whereExecutorId($this->specialist->id)
            ->where('status', '!=', 'finish')
            ->get();

        $text = view("messages_specialist.order_in_work", compact("orders"));
        $this->bot->msg("$text")->kbd($this->button->my_orders($orders))->send();
    }

    private function confirm_submit_order($order_id): void
    {
        $order = Order::findOrFail($order_id);

        if ($order->status == 'finish') {
            $this->bot->eventAnswerSnackbar('Ошибка');
            $this->bot->reply('Нельзя сдать заказ');
            $this->bot->reply("Текущий статус: $order->status");
            return;
        }

        $order->update([
            'status' => 'checking'
        ]);

        $student_peer_id = User::findOrFail($order->user_id)->peer_id;

        $student_vk = new VKStudentController();
        $attachments = Attachment::where([
            'order_id' => $order_id,
            'from' => 'specialist'
        ]);

        $text = view('messages.specialist_submit_order_review', ['count' => $attachments->count()]);
        $student_vk->bot->msg($text)->send(id: $student_peer_id);
        $uri = "";
        foreach ($attachments->get() as $attachment) {
//            $student_vk->bot->msg(asset("storage/{$attachment->attachments['url']}"))->send(id: $student_peer_id);

            $uri = $uri . (asset("storage/{$attachment->attachments['url']}")) . "\n{$attachment->message}\n\n";
        }

        $studentButton = new Buttons();
        $student_vk->bot->msg($uri)->kbd($studentButton->accept_specialist_order($order_id))->send(id: $student_peer_id);

        $this->bot->eventAnswerSnackbar("Работа отправлена на проверку");
        $this->bot->msg('Работа отправлена на проверку заказчиком')->kbd($this->button->mainMenuButton())->send();
    }

}
