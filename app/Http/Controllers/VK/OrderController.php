<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Http\Controllers\YooKassa;
use App\Models\Log;
use App\Models\Order;
use App\Models\Response;
use App\Models\Specialist;
use App\Models\User;
use Carbon\Carbon;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Throwable;


class OrderController extends Controller
{
    public SimpleVK $bot;
    public User $user;

    public string $action;
    public string $data;
    public string $chat_with;

    public Buttons $button;

    public Order $order;
    private array $payload;
    /**
     * @var mixed|string
     */
    private int|null $response_id;

    public function __construct(SimpleVK $bot, User $user, array $payload)
    {
        $this->bot = $bot;
        $this->user = $user;

        $this->action = $payload['action'] ?? "null";
        $this->data = $payload['data'] ?? "null";
        $this->chat_with = $payload['chat_with'] ?? "null";
        $this->response_id = $payload['response_id'] ?? null;

        $this->payload = $payload;

        $this->order = new Order();
        $this->button = new Buttons();
    }

    /**
     * @throws SimpleVkException|Throwable
     */
    public function init(): void
    {
        // Управление логикой при формировании нового заказа
        $this->newOrderLogic();

        // Управление логикой Моих заказов
        $this->myOrdersLogic();

        // Управление логикой общения с исполнителем
        $this->executorLogic();
    }

    /**
     * @throws SimpleVkException
     */
    // Создать новый заказ -> показать Категории
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
        } elseif ($this->action == "show_next_page_subjects") {
            $this->show_next_page_subjects();
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
    // Сохранить выбранную категорию пользователем и показать список предметом в зависимости от выбранной категории

    private function newOrder(): void
    {
        // Устанавливаю куки пользователю, пока он формирует заказ и от него не требуется ввода сообщений
        $this->user->update([
            "cookie" => "new_order"
        ]);

        // После клика на "Новый заказ" создаем заказ в статусе Черновик (draft)
        $this->order->createEmptyOrder($this->user);

        $message = "Выберите предмет:";
        $this->bot->eventAnswerSnackbar("$message");
        $this->bot->msg("$message")->kbd($this->button->categories())->send();
    }

    /**
     * @throws SimpleVkException
     */
    // Сохранить предмет и показать список "С чем нужно помочь"

    private function newOrderSaveCategoryAndShowSubject(): void
    {
        $category_id = $this->data;
        $this->order->addCategory($this->user, $category_id);

        $message = "Выберите с чем нужна помощь";
        $this->bot->eventAnswerSnackbar("$message");
        $this->bot->msg("$message")->kbd($this->button->subjects($category_id))->send();
    }


    /**
     * @throws SimpleVkException
     */
    // Сохранить с чем необходима помощь и показать список сроков
    private function show_next_page_subjects()
    {
        $category_id = $this->payload['category_id'];
        $page = $this->payload['page'];

        $message = "Просмотр следующей страницы";
        $this->bot->eventAnswerSnackbar("$message");
        $this->bot->msg("$message")->kbd($this->button->subjects($category_id, $page))->send();
    }

    /**
     * @throws SimpleVkException
     * @throws Throwable
     */
    // Сохранить сроки, показать информацию о заказе, назначить куки для обработки текстовых сообщений

    private function newOrderSaveSubjectAndShowWhatYouNeedHelpWith(): void
    {
        $subject_id = $this->data;
        $this->order->addSubject($this->user, $subject_id);

        $message = "Выберите категорию работы";
        $this->bot->eventAnswerSnackbar("$message");
        $this->bot->msg("$message")->kbd($this->button->whatYouNeedHelpWith())->send();
    }

    /**
     * @throws SimpleVkException
     */
    // Опубликовать заявку

    private function newOrderSaveWhatYouNeedHelpWithAndShowDeadlines(): void
    {
        $need_help_with = $this->data;
        $this->order->addWhatYouNeedHelpWith($this->user, $need_help_with);

        $message = "Укажите требуемые сроки";
        $this->bot->eventAnswerSnackbar("Укажите сроки исполнения");
        $this->bot->msg("$message")->kbd($this->button->deadlines())->send();
    }


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
        $this->bot->eventAnswerSnackbar("Информация о вашем заказе");
        $this->bot->msg("$order_info")->kbd($this->button->publishOrder())->send();
    }


    private function publishOrder(): void
    {
        // Публикация заказа
        $order_id = $this->order->publishOrder($this->user);

        Log::add($this->user->id, "Создание заказа", Order::class, "$order_id");
        $this->user->update(["cookie" => null]);

        $this->bot->msg("Ваша заявка (№ $order_id) опубликована, ожидайте ответа помощников.")
            ->kbd($this->button->mainMenuButton())
            ->send();

        $this->sendNotificationToSpecialists($order_id);
    }

    private function sendNotificationToSpecialists(mixed $order_id)
    {

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

    /** Список заказов пользователя
     * @throws SimpleVkException
     */
    private function myOrders(): void
    {
        if (Order::where(["user_id" => $this->user->id])->count() == 0) {
            $message = "У вас не найдено заказов";
        } else {
            $message = "Выберите заказ";
        }
        $this->bot->eventAnswerSnackbar("$message");
        $this->bot->msg("$message")->kbd($this->button->getOrdersByUser($this->user))->send();
    }

    /**
     * Показать информацию о заказе
     * @throws SimpleVkException|Throwable
     */
    private function _viewOrder(): void
    {
        $order_id = $this->data;
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
        $order_id = $this->data;
        $order = Order::findOrFail($order_id);
        $order->delete();

        $message = "Ваша заявка удалена.";
        $this->bot->eventAnswerSnackbar("Заявка удалена");
        $this->bot->msg("$message")->kbd($this->button->getOrdersByUser($this->user))->send();
    }

    /**
     * @throws SimpleVkException
     */
    private function _boostSearchSpecialist(): void
    {
        $order_id = $this->data;
        $order = Order::findOrFail($order_id);

        $this->bot->eventAnswerSnackbar("Ускорить поиск исполнителя");

        $message = view("messages.boostSearchSpecialist")->render();
        $this->bot->msg("$message")->kbd($this->button->boostSearchSpecialist($order))->send();

    }

    private function executorLogic()
    {
        if ($this->action == 'chat_with') {
            $this->bot->eventAnswerSnackbar("Отправьте сообщение");
            $this->bot->reply("Вы начали чат с специалистом. Отправьте сообщение.");
            $this->user->update([
                "cookie" => "chat_with_" . $this->chat_with . "|" . "order_id_" . $this->data,
            ]);
        }
        if ($this->action == "accept_offer") {
            $this->bot->eventAnswerSnackbar("Необходимо оплатить заказ");

            $response = Response::where(["order_id" => $this->data, 'id' => $this->response_id])->firstOrFail();
            $yookassa = new YooKassa();
            if ($payment = $yookassa->create(amount: $response->price, order_id: $response->order_id, response_id: $response->id)) {
                $this->bot->reply("Для оплаты заказа перейдите по ссылке: $payment");
            }

            Response::setStatus("$this->data", $response->executor_id, "accepted");
            Log::add(user_id: $this->user->id, action: "Предложение исполнителя принято", class: Order::class, action_id: $this->data);
        }

        if ($this->action == "yes_accept_specialist_and_finish_order") {
            $this->yes_accept_specialist_and_finish_order($this->data);
        }

        if ($this->action == 'no_decline_specialist_submit_work') {
            $this->no_decline_specialist_submit_work($this->data);
        }
    }

    private function yes_accept_specialist_and_finish_order($order_id)
    {
        $order = Order::findOrFail($order_id);
        if ($order->status != 'checking') return;

        $this->bot->msg('Заказ принят у исполнителя')->kbd($this->button->mainMenuButton())->send();
        $this->bot->eventAnswerSnackbar('Заказ принят');

        $order->update([
            'completion_date' => Carbon::now(),
            'status' => 'finish',
        ]);

        $specialist_bot = new VKSpecialistController();
        $specialist = Specialist::findOrFail($order->executor_id);
        $specialist_bot->bot->msg("Заказ №$order_id принят заказчиком.")->send($specialist->peer_id);

        $response = Response::where([
            'order_id' => $order->id,
            'executor_id' => $order->executor_id,
            'status' => 'accepted'
        ])->firstOrFail();

        $balance = $specialist->balance + ($response->price * $specialist->percent / 100);
        $specialist->update([
            'balance' => $balance,
        ]);

        $specialist_bot->bot->msg("Ваш баланс {$specialist->balance}")->send($specialist->peer_id);
    }

    private function no_decline_specialist_submit_work($order_id)
    {
        $order = Order::findOrFail($order_id);
        if ($order->status != 'checking') {
            $this->bot->eventAnswerSnackbar('Ошибка');
            $this->bot->reply("Ошибка отклонения заказа. Заказ должен быть в статусе checking, текущий статус $order->status");
        }

        $specialist = Specialist::findOrFail($order->executor_id);
        $specialistBot = new VKSpecialistController();

        $specialistBot->bot->msg('Заказчик отклонил работу')->send($specialist->peer_id);
        $this->bot->msg('Начните чат с заказчиком')->kbd($this->button->start_chat_with($specialist->id, $order_id, true))->send();
        $this->bot->eventAnswerSnackbar('Начните чат');

        $order->update([
            'status' => 'progress'
        ]);
    }

}
