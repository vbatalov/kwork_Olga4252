<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    protected $fillable = [
        "user_id",
        "order_id",
        "attachments",
        "type",
        "message",
    ];

    protected $casts = [
        "attachments" => "array"
    ];

    /** Добавить вложения к ID заказу */
    public function addAttachmentToOrderId(User $user, $order_id, $attachments, $text = ''): void
    {
        foreach ($attachments as $type => $attachment) {
            foreach ($attachment as $item) {
                Attachment::create([
                    "user_id" => $user->id,
                    "order_id" => $order_id,
                    "type" => $type,
                    "attachments" => $item,
                    "message" => $text,
                ]);
            }
        }

    }
}
