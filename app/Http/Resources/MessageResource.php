<?php

namespace App\Http\Resources;

use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Message
 */
class MessageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "from" => $this->from,
            "sender" => $this->sender,
            "to" => $this->to,
            "order_id" => $this->order_id,
            "recipient" => $this->recipient,
            "message" => $this->message,
            "attachments" => $this->attachments,
            "diff" => Carbon::parse($this->created_at)->diffForHumans(short: true, parts: 1),
        ];
    }
}
