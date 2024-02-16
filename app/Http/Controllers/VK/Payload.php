<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;

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

    public function __construct(SimpleVK $bot, User $user, $payload)
    {
        $this->bot = $bot;
        $this->user = $user;
        $this->payload = $payload;

        $this->button = new Buttons();
    }

    /** Контроллер всех нажатий на кнопку */
    public function payloadController()
    {

        try {

            if (isset($this->payload)) {
                $this->bot->reply("Payload ...");

                /** Если клик по категории */
                if (isset($this->payload['is_category'])) {
                    $this->bot->reply("Debug: клик в категории");
                    return $this->payloadCategoryController();
                }

                /** События при клике в главном меню */
                if (isset($this->payload['is_mainMenu'])) {
                    $this->bot->reply("Debug: клик в главном меню");
//                    return $this->payloadMenuController();
                }
            }

        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }

        try {
            return $this->bot->msg("После клика на кнопку ничего не произошло")->send();
        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }

    private function payloadCategoryController()
    {
        return true;
    }

    /** События в главном меню */
    private function payloadMenuController()
    {
        try {
            if ($this->payload['data'] == "new_order") {
                $message = "New Order";
                return $this->bot->msg("$message")->kbd($this->button->categories(), false, false)->send();
            }
        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }
}
