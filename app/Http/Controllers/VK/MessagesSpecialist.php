<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Response;
use App\Models\Specialist;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Log;

class MessagesSpecialist extends Controller
{
    public SimpleVK $bot;
    public Specialist $specialist;

    public ButtonsSpecialist $button;

    public Attachment $attachments;

    public function __construct(SimpleVK $bot, Specialist $specialist)
    {
        $this->bot = $bot;
        $this->specialist = $specialist;

        $this->button = new ButtonsSpecialist(); // кнопки меню

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


            /** Предложение цены */
            if (stripos("$cookie", "offer_price_id_") !== false) {
                $this->setPrice($cookie, $text);
            }

            /** Примечание к заказу */
            if (stripos("$cookie", "offer_note_id_") !== false) {
                $order_id = str_replace("offer_note_id_", "", $cookie);
                $this->setNote($order_id, $text);
            }

        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
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

            $this->bot->reply("Цена сохранена. Теперь укажите примечание для заказчика, например, как вы будете решать его проблему.");

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


}
