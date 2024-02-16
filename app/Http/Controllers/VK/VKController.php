<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;

//use App\Models\Log;
use App\Models\User;

use DigitalStars\SimpleVK\Auth;
use DigitalStars\SimpleVK\Message;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\LongPoll;
use DigitalStars\SimpleVK\SimpleVkException;
use DigitalStars\SimpleVK\Streaming;
use DigitalStars\SimpleVK\Bot;

use Log as LogLaravel;
use Throwable;


class VKController extends Controller
{
    public string $confirm;
    public SimpleVK $bot;

    public function __construct()
    {
        $token = config("app.VK_TOKEN");
        $this->confirm = config('app.VK_CONFIRM');

        $this->bot = SimpleVK::create("$token", "5.120");
        $this->bot->setConfirm($this->confirm);


        $this->bot->setUserLogError("120637023");

    }

    public function diagnostics() {
        \DigitalStars\SimpleVK\Diagnostics::run();
    }

    public function controller()
    {
        try {

            /** Init Vars */
            $this->bot->initVars($peer_id, $user_id, $type, $message, $payload, $id, $attachments);

            /** Модель пользователя */
            $user = new User($this->bot->userInfo($peer_id));

            /** Обработка текстовых сообщений */
            if ($type == "message_new") {
                $messages = new Messages($this->bot, $user);
                return $messages->messageController();
            }

            /** Обработка нажатий на кнопки */
            if ($type == "message_event") {
               $payload = new Payload($this->bot, $user, $payload);
               $this->bot->eventAnswerSnackbar(null);
               return $payload->payloadController();
            }



        } catch (Throwable $t) {
            LogLaravel::error($t->getMessage());
        }

    }

}
