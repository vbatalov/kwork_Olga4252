<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

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

    public function saveLocalAttachment(User $user, $order_id, $attachments, $text = ''): void
    {

        foreach ($attachments as $type => $attachment) {
            foreach ($attachment as $item) {

                if ($type == 'photo') {
                    $fileName = uuid_create() . '.jpg';
                    $content = file_get_contents($item['orig_photo']['url']);
                } else {
                    $content = file_get_contents($item['url']);
                    $fileName = uuid_create() . ".{$item['ext']}";
                }

                $patch = "orders/$order_id/attachments/$fileName";
                Storage::disk('public')->put("$patch", $content);

                Attachment::create([
                    "user_id" => $user->id,
                    "order_id" => $order_id,
                    "type" => $type,
                    "attachments" => ["url" => $patch],
                    "message" => $text,
                ]);
            }
        }
    }
}
