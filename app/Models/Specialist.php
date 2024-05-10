<?php

namespace App\Models;

use App\Http\Controllers\VK\Buttons;
use App\Http\Controllers\VK\VKSpecialistController;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    protected $table = "users";

    public VKSpecialistController $vk;

    public function __construct()
    {
        $this->vk = new VKSpecialistController();
    }

    public function init($user_info): User
    {
        /** Если пользователь новый, отправляю приветственное сообщение */
        if (!User::where("peer_id", $user_info['id'])->exists()) {
//            $this->messageStart();
        }

        /** Инициализация пользователя и/или создание нового */

        $user = User::updateOrCreate(
            [
                "peer_id" => $user_info['id'],
            ],
            [
                "name" => $user_info['first_name'],
                "surname" => $user_info['last_name'],
            ]
        );

        $this->user = $user;
        return $user;
    }

    private function messageStart(): void
    {
        $button = new Buttons();
        $message = view("messages.start")->render();
        $this->vk->bot->msg("$message")->kbd($button->mainMenu())->send();
    }
}
