<?php

namespace App\Models;

use App\Http\Controllers\VK\Buttons;
use App\Http\Controllers\VK\ButtonsSpecialist;
use App\Http\Controllers\VK\VKSpecialistController;
use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    protected $table = "specialists";
    protected $fillable = [
        "peer_id",
        "name",
        "surname",
        "role",
        "percent",
        "cookie",
    ];

    public VKSpecialistController $vk;

    public function __construct()
    {
        $this->vk = new VKSpecialistController();
    }

    public function init($user_info): Specialist
    {
        /** Если пользователь новый, отправляю приветственное сообщение */
        if (!$this::where("peer_id", $user_info['id'])->exists()) {
            $this->messageStart();
        }

        /** Инициализация пользователя и/или создание нового */

        return $this::updateOrCreate(
            [
                "peer_id" => $user_info['id'],
            ],
            [
                "name" => $user_info['first_name'],
                "surname" => $user_info['last_name'],
            ]
        );


    }

    private function messageStart(): void
    {
        $button = new ButtonsSpecialist();
        $message = view("messages_specialist.start")->render();
        $this->vk->bot->msg("$message")->kbd($button->mainMenu())->send();
    }
}
