<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\Specialist;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Log;
use Throwable;


class PayloadSpecialist extends Controller
{
    public SimpleVK $bot;
    public Specialist $specialist;

    public array $payload;
    public string $data;

    public ButtonsSpecialist $button;

    public Order $order;

    public function __construct(SimpleVK $bot, Specialist $specialist, $payload)
    {
        $this->bot = $bot;
        $this->specialist = $specialist;
        $this->payload = $payload;

        $this->order = new Order();

        $this->button = new ButtonsSpecialist();
    }

    /** Контроллер всех нажатий на кнопку */
    public function SpecialistPayloadController()
    {
        try {
            if (isset($this->payload) and (isset($this->payload['action']))) {

                $action = $this->payload['action'];

                // Контроллер заказов
                $orderController = new OrderSpecialistController($this->bot, $this->specialist, $this->payload);
                $orderController->init();


                /** Возврат в главное меню */
                if ($action == "return_to_home") {
                    $message = view("messages_specialist.start",
                    [
                        "specialist" => $this->specialist
                    ]);
                    $this->specialist->update([
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
