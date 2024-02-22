<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public SimpleVK $bot;
    public User $user;

    public array $payload;
    public string $data;

    public Buttons $button;

    public Order $order;

    public function __construct(SimpleVK $bot, User $user, array $payload)
    {
        $this->bot = $bot;
        $this->user = $user;
        $this->payload = $payload;

        $this->order = new Order();
        $this->button = new Buttons();
    }

    /**
     * @throws SimpleVkException
     */
    public function init()
    {
        $action = $this->payload['action'] ?? null;
        $data = $this->payload['data'] ?? null;

        // Список моих заказов
        if ($action == "my_orders") {
            return $this->myOrders();
        }

        // Просмотр заказа и управление
        if ($action == "view_order") {
            return $this->_viewOrder();
        }
        // Удаление заказа
        if ($action == "delete_order") {
            return $this->_deleteOrder();
        }
        // Ускорить поиск специалиста
        if ($action == "boost_search_specialist") {
            return $this->_boostSearchSpecialist();
        }
        // Ускорить поиск специалиста: Оплата Сбербанк
        if ($action == "payBoostSearchSpecialist_bySberbank") {
            $this->bot->eventAnswerSnackbar("Следуйте инструкции для оплаты");
            return $this->bot->reply("Инструкция об оплате на карту Сбербанк появится здесь");
        }

        return true;
    }

    /** Список заказов пользователя */
    private function myOrders()
    {
        $message = "Выберите заказ";
        $this->bot->eventAnswerSnackbar("$message");
        return $this->bot->msg("$message")->kbd($this->button->getOrdersByUser($this->user))->send();
    }

    /**
     * Показать информацию о заказе
     * @throws SimpleVkException
     */
    private function _viewOrder()
    {
        $order_id = $this->payload['data'];
        $order = Order::findOrFail($order_id);

        $message = view("messages.order_view", compact("order"))->render();
        $this->bot->eventAnswerSnackbar("Управлением заказом");
        return $this->bot->msg("$message")->kbd($this->button->orderActions($order))->send();

    }

    /** Удаление заказа
     * @throws SimpleVkException
     */
    private function _deleteOrder()
    {
        $order_id = $this->payload['data'];
        $order = Order::findOrFail($order_id);
        $order->delete();

        $message = "Ваша заявка удалена.";
        $this->bot->eventAnswerSnackbar("Заявка удалена");
        return $this->bot->msg("$message")->kbd($this->button->getOrdersByUser($this->user))->send();
    }

    private function _boostSearchSpecialist()
    {
        $order_id = $this->payload['data'];
        $order = Order::findOrFail($order_id);

        $this->bot->eventAnswerSnackbar("Ускорить поиск исполнителя");

        $message = view("messages.boostSearchSpecialist")->render();
        return $this->bot->msg("$message")->kbd($this->button->boostSearchSpecialist($order))->send();

    }

}
