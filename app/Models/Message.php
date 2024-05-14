<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable =
        [
            "from", "sender",
            "to", "recipient",
            "order_id",
            "message",
            "attachments",
        ];

    protected $casts = ["attachments" => "array"];

    public static function add($from, $sender, $to, $recipient, $order_id, $message = null, $attachments = []): void
    {
        Message::create([
            "from" => $from, "sender" => $sender,
            "to" => $to, "recipient" => $recipient,
            "order_id" => $order_id,
            "message" => $message,
            "attachments" => $attachments
        ]);
    }
}
