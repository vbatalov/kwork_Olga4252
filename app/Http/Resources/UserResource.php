<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            "peer_id" => $this->peer_id,
            "name" => $this->name,
            "surname" => $this->surname,
            "created_at" => Carbon::parse($this->created_at)->format("d.m.Y")
        ];
    }
}
