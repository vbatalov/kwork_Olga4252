<?php

namespace App\Http\Controllers\VK;

use App\Http\Controllers\Controller;
use App\Models\User;
use DigitalStars\SimpleVK\SimpleVK;
use DigitalStars\SimpleVK\SimpleVkException;
use Log;

class Messages extends Controller
{
    public SimpleVK $bot;
    public User $user;

    public Buttons $button;

    public function __construct(SimpleVK $bot, User $user)
    {
        $this->bot = $bot;
        $this->user = $user;

        $this->button = new Buttons();
    }


    public function messageController()
    {
        try {
            /** Cookie */
            $cookie = $this->user->getCookie();

            if ($cookie == null) {
                $message = view("messages.start");
                return $this->bot->msg("$message")->kbd($this->button->mainMenu(), false, True)->send();
            }

        } catch (SimpleVkException $e) {
            Log::error($e->getMessage());
        }

        return true;
    }




}
