<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected array $fillable = [
        "peer_id",
        "name",
        "surname",
        "role",
        "percent",
    ];


    public User $user;
    public string $peer_id;
    public array $userInfo;

    public function __construct(array $userInfo = null)
    {
        if ($userInfo != null) {
            /** Информация о пользователя */
            $this->userInfo = $userInfo;

            /** Инициализация пользователя */
            $this->peer_id = $userInfo['id'];
            $this->init();
        }

    }

    /**
     * Инициализирует пользователя
     * Создает, если нет в БД
     * Обновляет данные
     */
    private function init(): void
    {
        /** Инициализация пользователя или создание нового */
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


}
