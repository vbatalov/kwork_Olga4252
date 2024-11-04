<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Message;
use App\Models\Order;
use App\Models\Specialist;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Log;
use Throwable;

class Messages extends Controller
{
    public SimpleVK $bot;
    public User $user;

    public Buttons $button;
    public ButtonsSpecialist $buttonsSpecialist;

    public Attachment $attachments;

    public function __construct(SimpleVK $bot, User $user)
    {
        $this->bot = $bot;
        $this->user = $user;

        $this->button = new Buttons(); // кнопки меню
        $this->buttonsSpecialist = new ButtonsSpecialist(); // кнопки меню

        $this->attachments = new Attachment(); // вложения
    }


    public function StudentMessageController($attachments, $text)
    {
        try {
            /** Cookie */
            $cookie = $this->user->cookie;

            if (stripos("$cookie", "chat_with_") !== false) {
                return $this->sendMessageToSpecialist($cookie, $text);
            }

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

            // Если пользователь отправляет вложения к определённому заказу
            // add_attachments_student_$idOrder
            if ((stripos("$cookie", "add_attachments_student_order") !== false)) {
//                $this->bot->reply("DEBUG: Order: $order_id");
                $order_id = str_replace("add_attachments_student_order=", "", $cookie);
                // Проверяю, отправил ли пользователь вложение
                if (!empty($attachments)) {
                    $this->attachments->saveLocalAttachment("student", $order_id, $attachments, $text);
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
        } catch (Throwable $e) {
            $this->bot->reply($e->getMessage());
            $this->bot->reply("Лог в файле Message.php (Controller)");
            Log::error($e->getMessage());
        }

        return true;
    }

    /**
     * @param mixed $cookie
     * @param $text
     * @return bool
     * @throws SimpleVkException|Throwable
     */
    public function sendMessageToSpecialist(mixed $cookie, $text): bool
    {
        $str_replace = str_replace(["chat_with_", "order_id_"], "", $cookie);
        $array = explode("|", $str_replace);

        $specialist_id = $array[0];
        $order_id = $array[1];

        $specialist = Specialist::findOrFail($specialist_id);

        $VKSpecialistController = new VKSpecialistController();
        $from = "user";

        $buttonAlert = true;
        if (stripos($specialist->cookie, "|order_id_$order_id") !== false) {
            $buttonAlert = false;
        }
        $message = view("private_message", compact("text", "from", "order_id", "buttonAlert"))->render();


        $VKSpecialistController->bot->msg("$message")
            ->kbd($this->buttonsSpecialist->start_chat_with(user_id: $this->user->id, order_id: $order_id, buttonAlert: $buttonAlert), true)
            ->send($specialist->peer_id);

        Message::add(from: $this->user->id, sender: "user", to: $specialist->id, recipient: "specialist", order_id: $order_id, message: $text);

        $this->bot->reply("Сообщение отправлено.");
        return true;
    }


}
