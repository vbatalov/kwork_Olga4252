<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Illuminate\Database\Eloquent\Model;
use Throwable;


class OrderController extends Controller
{
    public SimpleVK $bot;
    public User $user;

    public string $action;
    public string $data;

    public Buttons $button;

    public Order $order;

    public function __construct(SimpleVK $bot, User $user, array $payload)
    {
        $this->bot = $bot;
        $this->user = $user;

        $this->action = $payload['action'] ?? "null";
        $this->data = $payload['data'] ?? "null";

        $this->order = new Order();
        $this->button = new Buttons();
    }

    /**
     * @throws SimpleVkException
     */
    public function init(): void
    {
        // Управление логикой при формировании нового заказа
        $this->newOrderLogic();

        // Управление логикой Моих заказов
        $this->myOrdersLogic();
    }

    /**
     * @throws SimpleVkException
     */
    // Создать новый заказ -> показать Категории
    private function newOrder(): void
    {
        // Устанавливаю куки пользователю, пока он формирует заказ и от него не требуется ввода сообщений
        $this->user->update([
            "cookie" => "new_order"
        ]);

        // После клика на "Новый заказ" создаем заказ в статусе Черновик (draft)
        $this->order->createEmptyOrder($this->user);

        $message = "Выберите направление заявки";
        $this->bot->eventAnswerSnackbar("$message");
        $this->bot->msg("$message")->kbd($this->button->categories())->send();
    }

    /**
     * @throws SimpleVkException
     */
    // Сохранить выбранную категорию пользователем и показать список предметом в зависимости от выбранной категории
    private function newOrderSaveCategoryAndShowSubject(): void
    {
        $category_id = $this->data;
        $this->order->addSubject($this->user, $category_id);

        $message = "Выберите с чем нужна помощь";
        $this->bot->msg("$message")->kbd($this->button->subjects($category_id))->send();
    }

    /**
     * @throws SimpleVkException
     */
    // Сохранить предмет и показать список "С чем нужно помочь"
    private function newOrderSaveSubjectAndShowWhatYouNeedHelpWith(): void
    {
        $subject_id = $this->data;
        $this->order->addSubject($this->user, $subject_id);

        $message = "Выберите с чем нужна помощь";
        $this->bot->msg("$message")->kbd($this->button->whatYouNeedHelpWith())->send();
    }


    /**
     * @throws SimpleVkException
     */
    // Сохранить с чем необходима помощь и показать список сроков
    private function newOrderSaveWhatYouNeedHelpWithAndShowDeadlines(): void
    {
        $need_help_with = $this->data;
        $this->order->addWhatYouNeedHelpWith($this->user, $need_help_with);

        $message = "Укажите требуемые сроки";
        $this->bot->msg("$message")->kbd($this->button->deadlines())->send();
    }

    /**
     * @throws SimpleVkException
     * @throws Throwable
     */
    // Сохранить сроки, показать информацию о заказе, назначить куки для обработки текстовых сообщений
    private function newOrderSaveDeadlineAndAddAttachmentsAndNotes(): void
    {
        // save deadlines
        $deadline = $this->data;
        $this->order->addDeadline($this->user, $deadline);

        // Получить ИД заказа, чтобы внести куки и затем обработать вложения по этому заказу
        $order = new Order();
        $order = $order->getDraftOrder($this->user);
        // обновить куки
        $this->user->update([
            "cookie" => "add_attachments_student_order=$order->id"
        ]);

        // Вывести информацию о заказе
        $order_info = $order->getInfoByOrderId($order->id);
        $this->bot->msg("$order_info")->kbd($this->button->publishOrder())->send();
    }

    /**
     * @throws SimpleVkException
     */
    // Опубликовать заявку
    private function publishOrder(): void
    {
        // Публикация заказа
        $order_id = $this->order->publishOrder($this->user);

        $this->bot->msg("Ваша заявка (№ $order_id) опубликована, ожидайте ответа помощников.")
            ->kbd($this->button->mainMenuButton())
            ->send();
    }


    /**
     * @throws SimpleVkException
     * @throws Throwable
     */
    private function newOrderLogic(): void
    {
        // Нажата кнопка Новый заказ
        if ($this->action == "new_order") {
            $this->newOrder();
        }
        // Пользователь выбрал категорию (save) -> Показать ему предметы
        if ($this->action == "newOrderSaveCategoryAndShowSubject") {
            $this->newOrderSaveCategoryAndShowSubject();
        }

        // Пользователь выбрал предмет (save) -> Показать ему список с чем нужно помочь
        if ($this->action == "newOrderSaveSubjectAndShowWhatYouNeedHelpWith") {
            $this->newOrderSaveSubjectAndShowWhatYouNeedHelpWith();
        }

        // Пользователь выбрал с чем необходима помощь (save) -> Показать ему список сроков
        if ($this->action == "newOrderSaveWhatYouNeedHelpWithAndShowDeadlines") {
            $this->newOrderSaveWhatYouNeedHelpWithAndShowDeadlines();
        }

        // Пользователь выбрал сроки (save) -> Показать информацию о заказе,
        // При необходимости сохранить вложения и примечания
        if ($this->action == "newOrderSaveDeadlineAndAddAttachmentsAndNotes") {
            $this->newOrderSaveDeadlineAndAddAttachmentsAndNotes();
        }

        // Опубликовать новый заказ
        if (($this->action == "publish_order")) {
            $this->bot->eventAnswerSnackbar("Заявка опубликована");
            $this->publishOrder();
        }


    }

    /**
     * @throws SimpleVkException
     */
    private function myOrdersLogic(): void
    {
        // Список моих заказов
        if ($this->action == "my_orders") {
            $this->myOrders();
        }

        // Просмотр заказа и управление
        if ($this->action == "view_order") {
            $this->_viewOrder();
        }

        // Удаление заказа
        if ($this->action == "delete_order") {
            $this->_deleteOrder();
        }

        // Ускорить поиск специалиста
        if ($this->action == "boost_search_specialist") {
            $this->_boostSearchSpecialist();
        }

        // Ускорить поиск специалиста: Оплата Сбербанк
        if ($this->action == "payBoostSearchSpecialist_bySberbank") {
            $this->bot->eventAnswerSnackbar("Следуйте инструкции для оплаты");
            $this->bot->reply("Инструкция об оплате на карту Сбербанк появится здесь");
        }

    }

    /** Список заказов пользователя */
    private function myOrders(): void
    {
        if (Order::where("user_id", $this->user->id)->count() == 0) {
            $message = "У вас не найдено заказов";
        } else {
            $message = "Выберите заказ";
        }
        $this->bot->eventAnswerSnackbar("$message");
        $this->bot->msg("$message")->kbd($this->button->getOrdersByUser($this->user))->send();
    }

    /**
     * Показать информацию о заказе
     * @throws SimpleVkException
     */
    private function _viewOrder(): void
    {
        $order_id = $this->payload['data'];
        $order = Order::findOrFail($order_id);

        $message = view("messages.order_view", compact("order"))->render();
        $this->bot->eventAnswerSnackbar("Управлением заказом");
        $this->bot->msg("$message")->kbd($this->button->orderActions($order))->send();

    }

    /** Удаление заказа
     * @throws SimpleVkException
     */
    private function _deleteOrder(): void
    {
        $order_id = $this->payload['data'];
        $order = Order::findOrFail($order_id);
        $order->delete();

        $message = "Ваша заявка удалена.";
        $this->bot->eventAnswerSnackbar("Заявка удалена");
        $this->bot->msg("$message")->kbd($this->button->getOrdersByUser($this->user))->send();
    }

    private function _boostSearchSpecialist(): void
    {
        $order_id = $this->payload['data'];
        $order = Order::findOrFail($order_id);

        $this->bot->eventAnswerSnackbar("Ускорить поиск исполнителя");

        $message = view("messages.boostSearchSpecialist")->render();
        $this->bot->msg("$message")->kbd($this->button->boostSearchSpecialist($order))->send();

    }

}
