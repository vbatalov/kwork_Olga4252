<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        "user_id",
        "order_id",
        "attachments",
    ];

    protected $casts = [
        "attachments" => "array"
    ];

    /** Добавить вложения к ID заказу */
    public function addAttachmentToOrderId(User $user, $order_id, $attachments)
    {


        if (Attachment::where(["user_id" => $user->id, "order_id" => $order_id,])->exists()) {

            \Log::alert("Вложения уже в БД есть");

            // Предыдущие вложения
            $current_attachments = Attachment::where([
                "user_id" => $user->id,
                "order_id" => $order_id,
            ])->first();

            // Добавляю новое вложение
            $newAttachments = [
                $current_attachments->attachments,
                $attachments
            ];
            // Обновляю БД
            $current_attachments::update([
                "user_id" => $user->id,
                "order_id" => $order_id,
                "attachments" => ["sdf"],
            ]);
        } else {
            \Log::alert("Вложений нет");
            // Если вложений ещё нет, добавляю первое
            Attachment::create([
                "user_id" => $user->id,
                "order_id" => $order_id,
                "attachments" => $attachments,
            ]);
        }

        $result = Attachment::where(["user_id" => $user->id, "order_id" => $order_id,])->first();

        return \Log::alert(count($result->attachments));
    }
}
