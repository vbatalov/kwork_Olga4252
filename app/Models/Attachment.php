<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Storage;

class Attachment extends Model
{
    protected $fillable = [
        "from",
        "order_id",
        "attachments",
        "type",
        "message",
    ];

    protected $casts = [
        "attachments" => "array"
    ];

    // var $from = Enum [student, specialist]
    public function saveLocalAttachment(string $from, $order_id, $attachments, $text = ''): void
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
                    "from" => $from,
                    "order_id" => $order_id,
                    "type" => $type,
                    "attachments" => ["url" => $patch],
                    "message" => $text,
                ]);
            }
        }
    }
}
