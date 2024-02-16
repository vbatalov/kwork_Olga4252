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

                /** Если клик по категории */
                if (isset($this->payload['is_category'])) {
                    $this->bot->reply("Debug: клик в категории");
                    return $this->payloadCategoryController();
                }

                if (isset($this->payload['is_subject'])) {
                    $this->bot->reply("Debug: клик по предмету");
                    return $this->payloadSubjectController();
                }

                /** События при клике в главном меню */
                if (isset($this->payload['is_mainMenu'])) {
                    $this->bot->reply("Debug: клик в главном меню");
                    return $this->payloadMenuController();
                }

                /** Возврат в главное меню */
                if ($this->payload['data'] == "menu") {
                    $message = view("messages.start");
                    return $this->bot->msg("$message")->kbd($this->button->mainMenu())->send();
                }
            }

        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }


        return $this->bot->reply("После клика на кнопку ничего не произошло");

    }

    /** Отслеживание нажатий кнопок в категории
     * @throws SimpleVkException
     */
    private function payloadCategoryController()
    {
        /**  */
        $category_id = $this->payload['data'];

        $message = "Выберите предмет";
        $this->bot->msg("$message")->kbd($this->button->subjects($category_id) )->send();

    }

    private function payloadSubjectController() {
        $this->bot->reply("Клик по предмету");
    }

    /** События в главном меню */
    private function payloadMenuController()
    {
        try {
            if ($this->payload['data'] == "new_order") {
                $order = new Order();

                $message = "New Order";
                return $this->bot->msg("$message")->kbd($this->button->categories() )->send();
            }
        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }
}
