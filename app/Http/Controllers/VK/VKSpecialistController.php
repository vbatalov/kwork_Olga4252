<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Specialist;
use App\Models\User;
use DigitalStars\SimpleVK\Diagnostics;
use DigitalStars\SimpleVK\SimpleVK as vk;

use Log as LogLaravel;


class VKSpecialistController extends Controller
{
    public string $confirm;
    public vk $bot;

    public function __construct()
    {
        $token = config("app.VK_TOKEN_SPECIALIST");
        $this->confirm = config('app.VK_CONFIRM_SPECIALIST');

        $this->bot = vk::create("$token", "5.199");
        $this->bot->setConfirm($this->confirm);

//        $this->bot->setUserLogError("120637023");
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

            /** Модель специалиста */
            $specialist = new Specialist();
            $specialist = $specialist->init(user_info: $this->bot->userInfo($user_id));
            if ($specialist->count() < 1) {
                return $this->bot->reply("ERROR: User not identify");
            }

            /** Обработка нажатий на кнопки */
            if ($type == "message_event" or (isset($payload))) {
                $payload = new PayloadSpecialist($this->bot, $specialist, $payload); // init
                return $payload->SpecialistPayloadController();
            }

            /** Обработка текстовых сообщений */
            if ($type == "message_new") {
                $messages = new MessagesSpecialist($this->bot, $specialist); // init
                return $messages->SpecialistMessageController($this->bot->getAttachments(), $message);
            }


        } catch (\Throwable $t) {
            LogLaravel::error($t->getMessage());
            LogLaravel::error($t->getFile());
            LogLaravel::error($t->getLine());
        }

        return true;
    }
}
