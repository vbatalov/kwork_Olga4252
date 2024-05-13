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







}
