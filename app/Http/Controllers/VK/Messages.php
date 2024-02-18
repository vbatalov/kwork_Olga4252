<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Order;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Log;

class Messages extends Controller
{
    public SimpleVK $bot;
    public User $user;

    public Buttons $button;

    public Attachment $attachments;

    public function __construct(SimpleVK $bot, User $user)
    {
        $this->bot = $bot;
        $this->user = $user;

        $this->button = new Buttons(); // кнопки меню

        $this->attachments = new Attachment(); // вложения
    }


    public function StudentMessageController($attachments, $text)
    {
        try {
            /** Cookie */
            $cookie = $this->user->cookie;

            // Если куков нет, показать главное меню
            if ($cookie == null) {
                $message = view("messages.start");
                return $this->bot->msg("$message")->kbd($this->button->mainMenu())->send();
            }

            // Когда пользователь оформляет заказ, мы не даем обратную связь по кнопкам
            if ($cookie == "new_order") {
                $message = "Используйте кнопки меню при оформлении заказа, когда будет необходимо ввести информацию, мы дадим знать. Если вы не хотите продолжать, вернитесь в главное меню.";
                return $this->bot->msg("$message")->kbd($this->button->mainMenuButton(), true)->send();
            }

            // Если пользователь отправляет вложения к определнному заказу
            // add_attachments_student_$idOrder
            if ($order_id = str_replace("add_attachments_student_order=", "", $cookie)) {
//                $this->bot->reply("DEBUG: Order: $order_id");

                // Проверяю, отправил ли пользователь вложение
                if (!empty($attachments)) {
                    $this->attachments->addAttachmentToOrderId($this->user, $order_id, $attachments);
                    return $this->bot->msg("Мы добавили вложение в заказ. При необходимости, можете добавить ещё вложения или отправьте заявку.")->kbd($this->button->publishOrder())->send();
                } else {
                    $order = Order::findOrFail($order_id);
                    $order->update([
                        "description" => $text
                    ]);
                    $message = view("messages.order_info", compact("order"))->render();
                    return $this->bot->reply("$message");
                }
            }


        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }


}
