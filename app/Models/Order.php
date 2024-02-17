<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        "user_id",
        "category_id",
        "subject_id",
        "need_help_with",
        "description",
        "deadline",
        "status",
        "executor_id",
        "completion_date",
    ];

    protected $casts = [
        "deadline" => "string"
    ];

    /** Создание заказа при клике "Новый заказ"
     * Создает новый черновик (если заказа не было, или возвращает последний активный черновик)
     */
    public function createEmptyOrder(User $user): void
    {
        Order::where("user_id", $user->id)
            ->where("status", "draft")->firstOrCreate([
                "user_id" => $user->id
            ]);
    }

    /** Добавить категорию в заказ */
    public function addCategory(User $user, $category_id): bool
    {
        $order = $this->getDraftOrder($user);

        $order->update([
            "category_id" => $category_id
        ]);

        return true;
    }

    /** Добавить предмет в заказ */
    public function addSubject(User $user, $subject_id): bool
    {
        $order = $this->getDraftOrder($user);

        $order->update([
            "subject_id" => $subject_id
        ]);

        return true;
    }

    /** Указать с чем нужна помощь */
    public function addWhatYouNeedHelpWith(User $user, $WhatYouNeedHelpWith): bool
    {
        $order = $this->getDraftOrder($user);

        $order->update([
            "need_help_with" => $WhatYouNeedHelpWith,
        ]);

        return true;
    }

    public function addDeadline(User $user, $deadline): bool
    {
        $order = $this->getDraftOrder($user);

        $order->update([
            "deadline" => $deadline
        ]);

        return true;
    }

    /** Функция возвращает первый существующий черовник заказа */
    private function getDraftOrder(User $user): Order
    {
        return Order::where("user_id", $user->id)
            ->where("status", "draft")
            ->firstOrFail();
    }
}
