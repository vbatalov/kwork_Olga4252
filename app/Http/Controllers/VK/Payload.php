<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Log;
use Throwable;


class Payload extends Controller
{
    public SimpleVK $bot;
    public User $user;

    public array $payload;
    public string $data;

    public Buttons $button;

    public Order $order;

    public function __construct(SimpleVK $bot, User $user, $payload)
    {
        $this->bot = $bot;
        $this->user = $user;
        $this->payload = $payload;

        $this->order = new Order();

        $this->button = new Buttons();
    }

    /** Контроллер всех нажатий на кнопку */
    public function StudentPayloadController()
    {
        try {
            if (isset($this->payload) and (isset($this->payload['action']))) {

                $action = $this->payload['action'];

                // Контроллер заказов
                // Создание, просмотр, удаление, оплата
                $orderController = new OrderController($this->bot, $this->user, $this->payload);
                $orderController->init();


                /**
                 * Если клик по категории
                 * Пользователь выбирает Категорию (например, математика) затем ему нужно выбрать предмет
                 */
                if (($action== "click_from_category")) {
                    return $this->_CategoryController();
                }

                // Клик по предмету, затем нужно выбрать с чем помочь
                if (($this->payload['action']) == "click_from_subject") {
                    $this->bot->eventAnswerSnackbar("Выберите с чем нужна помощь");
                    return $this->_SubjectController();
                }

                // Клик с чем нужна помощь, затем выбор сроков
                if (($action== "click_from_is_whatYouNeedHelpWith")) {
                    $this->bot->eventAnswerSnackbar("Укажите требуемые сроки");
                    return $this->_WhatYouNeedHelpWith();
                }

                // Клик по срокам, затем ...
                if (($action== "click_from_deadline")) {
                    $this->bot->eventAnswerSnackbar("При необходимости добавьте вложения и/или отправьте заказ");
                    return $this->_Deadline();
                }

                // Клик отправить заявку
                if (($action== "publish_order")) {
                    $this->bot->eventAnswerSnackbar("Заявка опубликована");
                    return $this->_publishOrder();
                }


                /** Возврат в главное меню */
                if ($action == "return_to_home") {
                    $message = view("messages.start");
                    $this->user->update([
                        "cookie" => null
                    ]);

                    $this->bot->eventAnswerSnackbar("Главное меню");
                    return $this->bot->msg("$message")->kbd($this->button->mainMenu())->send();
                }
            }


        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }


        $this->bot->eventAnswerSnackbar("После клика на кнопку ничего не произошло");
        return $this->bot->reply("После клика на кнопку ничего не произошло");

    }

    /** Отслеживание нажатий кнопок в категории
     * @throws SimpleVkException
     */
    private function _CategoryController()
    {
        /** Идентифицирую ID категории */
        if (isset($this->payload['data'])) {
            $category_id = $this->payload['data'];
            /** Добавляю категорию в заказ */
            if (!$this->order->addCategory($this->user, $category_id)) {
                $this->bot->reply("Не удалось добавить категорию в заказ (ошибка БД).");
            }

            $this->bot->eventAnswerSnackbar("Выберите предмет");
            $message = "Выберите предмет";
            return $this->bot->msg("$message")->kbd($this->button->subjects($category_id))->send();
        }

        return $this->bot->reply("Ошибка при формировании предметов в зависимости от категорий");
    }

    /**  Клик по теме, отправка клавиатуры для выбора "С чем нужна помощь" */
    private function _SubjectController()
    {
        if(isset($this->payload['data'])) {
            $subject_id = $this->payload['data'];
            $this->order->addSubject($this->user, $subject_id);
        }

        $message = "Выберите с чем нужна помощь";
        return $this->bot->msg("$message")->kbd($this->button->whatYouNeedHelpWith())->send();
    }

    /** Клик "С чем нужна помощь". Отправка клавиатуры со сроками */
    private function _WhatYouNeedHelpWith()
    {
        if (isset($this->payload['data'])) {
            $need_help_with = $this->payload['data'];
            if (!$this->order->addWhatYouNeedHelpWith($this->user, $need_help_with)) {
                return $this->bot->reply("Не удалось добавить в БД информацию об объекте помощи (_WhatYouNeedHelpWith)");
            }
        }


        $message = "Укажите требуемые сроки";
        return $this->bot->msg("$message")->kbd($this->button->deadlines())->send();
    }

    /** Выбор сроков. Затем выводим информацию о заказе и просим добавить файлы
     * @throws Throwable
     */
    private function _Deadline()
    {
        $deadline = $this->payload['data'];
        if (!$this->order->addDeadline($this->user, $deadline)) {
            return $this->bot->reply("Не удалось добавить в БД о сроках (_Deadline)");
        }

        // Получить ИД заказа, чтобы внести куки и затем обработать вложения по этому заказу
        $order = new Order();
        $order = $order->getDraftOrder($this->user);

        $this->user->update([
            "cookie" => "add_attachments_student_order=$order->id"
        ]);

        // Информация о заказе
        $order_info = $order->getInfoByOrderId($order->id);
        return $this->bot->msg("$order_info")->kbd($this->button->publishOrder())->send();
    }

    /** Отправка заявки
     * @throws SimpleVkException
     */
    private function _publishOrder()
    {
        // Публикация заказа
        if (!$this->order->publishOrder($this->user)) {
            return $this->bot->reply("При публикации заявки произошла ошибка");
        }
        return $this->bot->msg("Ваша заявка опубликована, ожидайте ответа помощников.")->kbd($this->button->mainMenuButton())->send();
    }

}
