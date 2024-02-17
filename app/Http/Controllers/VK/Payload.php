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
            if (isset($this->payload)) {

                /**
                 * Если клик по категории
                 * Пользователь выбирает Категорию (например, математика) затем ему нужно выбрать предмет
                 */
                if (isset($this->payload['is_category'])) {
                    $this->bot->eventAnswerSnackbar("Выберите предмет");
                    return $this->_CategoryController();
                }

                // Клик по предмету, затем нужно выбрать с чем помочь
                if (isset($this->payload['is_subject'])) {
                    $this->bot->eventAnswerSnackbar("Выберите с чем нужна помощь");
                    return $this->_SubjectController();
                }

                // Клик с чем нужна помощь, затем выбор сроков
                if (isset($this->payload['is_whatYouNeedHelpWith'])) {
                    $this->bot->eventAnswerSnackbar("Укажите требуемые сроки");
                    return $this->_WhatYouNeedHelpWith();
                }

                // Клик по срокам, затем ...
                if (isset($this->payload['is_deadline'])) {
                    $this->bot->eventAnswerSnackbar("Укажите...");
                    return $this->_Deadline();
                }

                /** События при клике в главном меню */
                if (isset($this->payload['is_mainMenu'])) {
                    $this->bot->reply("Debug: клик в главном меню");
                    $this->bot->eventAnswerSnackbar("Вы сделали клик из главного меню");

                    return $this->_MenuController();
                }

                /** Возврат в главное меню */
                if ($this->payload['data'] == "menu") {
                    $message = view("messages.start");
                    $this->bot->eventAnswerSnackbar("Главное меню");
                    return $this->bot->msg("$message")->kbd($this->button->mainMenu())->send();
                }
            }

        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }


        return $this->bot->reply("После клика на кнопку ничего не произошло");

    }

    /** Отслеживание нажатий кнопок в категории
     * @throws SimpleVkException
     */
    private function _CategoryController()
    {
        /** Идентифицирую ID категории */
        $category_id = $this->payload['data'];
        /** Добавляю категорию в заказ */
        if (!$this->order->addCategory($this->user, $category_id)) {
            $this->bot->reply("Не удалось добавить категорию в заказ (ошибка БД).");
        }

        $message = "Выберите предмет";
        return $this->bot->msg("$message")->kbd($this->button->subjects($category_id) )->send();
    }

    /**  Клик по теме, отправка клавиатуры для выбора "С чем нужна помощь" */
    private function _SubjectController() {
        $subject_id = $this->payload['data'];
        $this->order->addSubject($this->user, $subject_id);

        $message = "Выберите с чем нужна помощь";
        return $this->bot->msg("$message")->kbd($this->button->whatYouNeedHelpWith())->send();
    }

    /** Клик "С чем нужна помощь". Отправка клавиатуры со сроками */
    private function _WhatYouNeedHelpWith()
    {
        $need_help_with = $this->payload['data'];
        if (!$this->order->addWhatYouNeedHelpWith($this->user, $need_help_with)) {
            return $this->bot->reply("Не удалось добавить в БД информацию об объекте помощи (_WhatYouNeedHelpWith)");
        }

        $message = "Укажите требуемые сроки";
        return $this->bot->msg("$message")->kbd($this->button->deadlines())->send();
    }

    /** Выбор сроков. Затем выводим информацию о заказе и просим добавить файлы */
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

        $this->bot->reply("Информация о заказе. Добавьте вложения");
//        $this->bot->msg("Здесь информация о заказе. Добавьте вложения")->kbd($this->button->mainMenuButton(), true)->send();

        return true;
    }

    /** События в главном меню */
    private function _MenuController()
    {
        try {
            if ($this->payload['data'] == "new_order") {
                // Устанавливаю куки пользователю, пока он формирует заказ и от него не требуется ввода сообщений
                $this->user->update([
                    "cookie" => "new_order"
                ]);

                // После клика на "Новый заказ" создаем заказ в статусе Черновик (draft)
                $this->order->createEmptyOrder($this->user);


                $message = "Выберите категорию.";
                return $this->bot->msg("$message")->kbd($this->button->categories())->send();
            }

        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }
}
