<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function getAvailableOrders($offset = 0)
    {
        return Order::where("status", "pending")->offset($offset)->limit(5)->get();
    }

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

    /** Функция возвращает первый существующий черовник заказа */
    public function getDraftOrder(User $user): Order
    {
        return Order::where("user_id", $user->id)
            ->where("status", "draft")
            ->firstOrFail();
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

    /**
     * @throws \Throwable
     */
    public function getInfoByOrderId($order_id): string
    {
        $order = Order::findOrFail($order_id);

        return view("messages.order_info", compact("order"))->render();
    }

    public function publishOrder(User $user)
    {
        $order = $this->getDraftOrder($user);
        $order->update([
            "status" => "pending"
        ]);

        return $order->id;
    }

    public static function setStatus($order_id, $status): void
    {
        Order::findOrFail($order_id)->update([
            "status" => "$status"
        ]);
    }

    public function category(): HasOne // Получить категорию
    {
        return $this->hasOne(Category::class, "id", "category_id");
    }

    public function subject(): HasOne // Получить предмет
    {
        return $this->hasOne(Subject::class, "id", "subject_id");
    }

    public function user(): HasOne // Получить пользователя
    {
        return $this->hasOne(User::class, "id", "user_id");
    }

    public function response(): HasOne // Получить отклик
    {
        return $this->hasOne(Response::class, "order_id", "id");
    }
}
