<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Order;
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

            // Если пользователь отправляет вложения к определнному заказу
            // add_attachments_student_$idOrder
            if ($order_id = str_replace("offer_price_id_", "", $cookie)) {
                $this->bot->msg("Order $order_id")->send();
            }


        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }


}
