<?php

namespace App\Models;


use App\Http\Controllers\VK\VKController;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected array $fillable = [
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

    public function __construct(array $userInfo = null)
    {
        $this->vk = new VKController();

        if ($userInfo != null) {
            /** Информация о пользователя */
            $this->userInfo = $userInfo;

            /** Инициализация пользователя */
            $this->peer_id = $userInfo['id'];
            $this->init();
        }

    }

    /** Начальное сообщение */
    private function messageStart()
    {
        $message = view("messages.start")->render();
        $this->vk->bot->buttonCallback("$message", 'white', ['action' => "menu"]);

    }

    /**
     * Инициализирует пользователя
     * Создает, если нет в БД
     * Обновляет данные
     */
    private function init(): void
    {
        /** Если пользователь новый, отправляю приветственное сообщение */
        if (!User::where("peer_id", $this->peer_id)->exists()) {
            $this->messageStart();
        }

        /** Инициализация пользователя и/или создание нового */
        $this->user = User::updateOrCreate(
            [
                "peer_id" => $this->peer_id,
            ],
            [
                "name" => $this->userInfo['first_name'],
                "surname" => $this->userInfo['last_name'],
            ]
        );


    }

    /** Установить cookie (для отслеживания сообщения) */
    public function setCookie(string $cookie): bool
    {
        return $this->user->update([
            "cookie" => $cookie
        ]);
    }

    /** Получить cookie */
    public function getCookie()
    {
        return $this->user->cookie;
    }

}
