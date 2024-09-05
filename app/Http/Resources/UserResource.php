<?php

namespace App\Http\Resources;

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "peer_id" => $this->peer_id,
            "name" => $this->name,
            "surname" => $this->surname,
            "created_at" => Carbon::parse($this->created_at)->format("d.m.Y"),
            "orders" => OrderResource::collection(Order::whereUserId($this->id)->get())
        ];
    }
}
