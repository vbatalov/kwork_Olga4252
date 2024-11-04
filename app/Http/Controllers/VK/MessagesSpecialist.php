<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Message;
use App\Models\Order;
use App\Models\Response;
use App\Models\Specialist;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Log;

class MessagesSpecialist extends Controller
{
    public SimpleVK $bot;
    public Specialist $specialist;

    public ButtonsSpecialist $button;
    public Buttons $buttonUser;

    public Attachment $attachments;

    public function __construct(SimpleVK $bot, Specialist $specialist)
    {
        $this->bot = $bot;
        $this->specialist = $specialist;

        $this->button = new ButtonsSpecialist(); // кнопки меню
        $this->buttonUser = new Buttons(); // кнопки меню

        $this->attachments = new Attachment(); // вложения
    }


    public function SpecialistMessageController($attachments, $text)
    {
        try {
            /** Cookie */
            $cookie = $this->specialist->cookie;

            // Если куков нет, показать главное меню
            if ($cookie == null) {
                $message = view("messages.start");
                return $this->bot->msg("$message")->kbd($this->button->mainMenu())->send();
            }

            if (stripos("$cookie", "chat_with_") !== false) {
                return $this->sendMessageToUser($cookie, $text);
            }
            if (stripos("$cookie", "cancel_response_") !== false) {
                return $this->confirm_cancel_response($cookie, $text);
            }

            /** Предложение цены */
            if (stripos("$cookie", "offer_price_id_") !== false) {
                $this->setPrice($cookie, $text);
            }

            /** Примечание к заказу */
            if (stripos("$cookie", "offer_note_id_") !== false) {
                $order_id = str_replace("offer_note_id_", "", $cookie);
                $this->setNote($order_id, $text);
            }

            /** Сдать работу */
            if (stripos("$cookie", "submit_work_") !== false) {
                $order_id = str_replace("submit_work_", "", $cookie);
                $this->submitWork($order_id, $attachments, $text);
            }

        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }

    private function sendMessageToUser(mixed $cookie, $text)
    {
        $str_replace = str_replace(["chat_with_", "order_id_"], "", $cookie);
        $array = explode("|", $str_replace);

        $user = $array[0];
        $order_id = $array[1];

        $user = User::findOrFail($user);

        $VKStudentController = new VKStudentController();
        $from = "specialist";

        $buttonAlert = true;
        if (stripos($user->cookie, "|order_id_$order_id") !== false) {
            $buttonAlert = false;
        }

        $message = view("private_message", compact("text", "from", "order_id", "buttonAlert"))->render();

        $VKStudentController->bot->msg("$message")
            ->kbd($this->buttonUser->start_chat_with(specialist_id: $this->specialist->id, order_id: $order_id, buttonAlert: $buttonAlert), true)
            ->send($user->peer_id);

        Message::add(from: $this->specialist->id, sender: "specialist", to: $user->id, recipient: "user", order_id: $order_id, message: $text);

        $this->bot->reply("Сообщение отправлено.");
        return true;

    }

    private function confirm_cancel_response(mixed $cookie, $text)
    {
        $order_id = str_replace("cancel_response_", "", $cookie);
        $confirm_text = "Удалить отклик $order_id";

        if ($text === $confirm_text) {
            $response = Response::where([
                "executor_id" => $this->specialist->id,
                "order_id" => $order_id,
            ])->firstOrFail();

            $response->delete(); // удалил отклик

            $order = Order::findOrFail($response->order_id);
            $user = User::findOrFail($order->user_id);

            $this->bot->reply("Ваш отклик на заказ удален.");

            $studentBot = new VKStudentController();
            $studentBot->bot->msg("Исполнитель удалил свой отклик на заказ №$order_id")->send($user->peer_id);

            // обнуляю куки
            $this->specialist->update([
                "cookie" => null
            ]);


        } else {
            $this->bot->reply("Ошибка. Для удаления заказа необходимо написать: $confirm_text. С учетом регистра (без знаков).");
        }

        return true;
    }

    /**
     * @param mixed $cookie
     * @param $text
     * @return array|mixed|string|string[]
     * @throws SimpleVkException
     */
    public function setPrice(mixed $cookie, $text): mixed
    {
        $order_id = str_replace("offer_price_id_", "", $cookie);
        $price = intval($text);

        if ($price > 0) {
            Response::addResponse(executor_id: $this->specialist->id, order_id: $order_id, price: $price);

            $price_with_percent = ($price * $this->specialist->percent) / 100;
            $this->bot->reply("Цена сохранена. За выполнение заказа Вы получите $price_with_percent рублей. Теперь укажите примечание для заказчика, например, как вы будете решать его проблему.");

            $this->specialist->update([
                    "cookie" => "offer_note_id_" . $order_id
                ]
            );

        } else {
            $this->bot->msg("Укажите только цену в рублях, без кириллицы.")->send();
        }
        return $order_id;
    }

    /**
     * @param string $order_id
     * @param $text
     * @return void
     * @throws SimpleVkException
     */
    public function setNote(string $order_id, $text): void
    {
        Response::addNoteToResponse(executor_id: $this->specialist->id, order_id: $order_id, note: $text);

        $this->bot->msg("Примечание обновлено. Теперь вы можете отправить предложение заказчику.")
            ->kbd($this->button->send_response(order_id: $order_id))->send();

        $this->specialist->update([
            "cookie" => null
        ]);
    }

    private function submitWork($order_id, $attachments = [], $text = "")
    {
        if (is_array($attachments) && count($attachments)) {
            $attachModel = new Attachment();
            $attachModel->saveLocalAttachment("specialist", $order_id, $attachments, "$text");
            $text = 'Прикрепили вложение к заказу. При необходимости добавьте ещё вложения или подтвердите выполнение заказа';
            $this->bot->msg($text)->kbd([
                [$this->button->confirmSubmitWork($order_id)],
                [$this->button->mainMenuButton()]
            ])->send();
        } else {
            $this->bot->reply("Вы не прикрепили вложение. Сдать заказ текстом нельзя.");
        }

    }


}
