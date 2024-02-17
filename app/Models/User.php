<?php

namespace App\Models;


use App\Http\Controllers\VK\Buttons;
use App\Http\Controllers\VK\VKController;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        "peer_id",
        "name",
        "surname",
        "role",
        "percent",
        "cookie",
    ];

    public User $user;

    public string $peer_id;
    public array $userInfo;

    public VKController $vk;

    public function __construct()
    {
        $this->vk = new VKController();
    }

    /** Начальное сообщение */
    private function messageStart(): void
    {
        $button = new Buttons();
        $message = view("messages.start")->render();
        $this->vk->bot->msg("$message")->kbd($button->mainMenu())->send();
    }

    /**
     * Инициализирует пользователя
     * Создает, если нет в БД
     * Обновляет данные
     * @throws \Throwable
     */
    public function init($user_info): User
    {
        /** Если пользователь новый, отправляю приветственное сообщение */
        if (!User::where("peer_id", $user_info['id'])->exists()) {
            $this->messageStart();
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

    /** Установить cookie (для отслеживания сообщения) */
    public function setCookie(User $user, string $cookie): bool
    {
        return $user->update([
            "cookie" => $cookie
        ]);
    }

    /** Получить cookie */
    public function getCookie()
    {
        return $this->user->cookie;
    }

}
