<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;


use App\Models\User;

use DigitalStars\SimpleVK\{Bot, Diagnostics, SimpleVK as vk};

use Log as LogLaravel;
use Throwable;

// TODO: в методе getAttachments удалить return 0 на второй строке

class VKController extends Controller
{
    public string $confirm;
    public vk $bot;

    public function __construct()
    {
        $token = config("app.VK_TOKEN");
        $this->confirm = config('app.VK_CONFIRM');

        $this->bot = vk::create("$token", "5.199");
        $this->bot->setConfirm($this->confirm);

        $this->bot->setUserLogError("120637023");
    }

    public function diagnostics()
    {
        Diagnostics::run();
    }

    public function controller()
    {
        try {

            /** Init Vars */
            $this->bot->initVars($peer_id, $user_id, $type, $message, $payload, $id, $attachments);

            /** Модель пользователя */
            $user = new User();
            $user = $user->init($this->bot->userInfo($peer_id));
            if ($user->count() < 1) {
                $this->bot->reply("ERROR: User not identify");
            } else {
//                $this->bot->reply("DEBUG: User identify");
            }

            /** Обработка нажатий на кнопки */
            if ($type == "message_event" or (isset($payload))) {
                $payload = new Payload($this->bot, $user, $payload); // init

                // Обработка нажатий на кнопки студента
                if ($user->role == 'student') {
                    return $payload->StudentPayloadController();
                }
            }

            /** Обработка текстовых сообщений */
            if ($type == "message_new") {
                $messages = new Messages($this->bot, $user); // init

                // Обработка сообщений от студента
                if ($user->role == 'student') {
//                    $this->bot->reply("DEBUG Role: student");
//                    $this->bot->reply("DEBUG Cookie: $user->cookie");

                    return $messages->StudentMessageController($this->bot->getAttachments());
                }

                if ($user->role == "specialist") {
                    $this->bot->reply("Обработка сообщений специалиста не настроена");
                }
            }


        } catch (Throwable $t) {
            LogLaravel::error($t->getMessage());
            LogLaravel::error($t->getFile());
            LogLaravel::error($t->getLine());
        }

        return true;
    }

}
