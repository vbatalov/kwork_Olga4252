<?php

namespace App\Http\Resources;

use App\Models\Category;
use App\Models\Order;
use App\Models\Subject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Order
 */
class OrderResource extends JsonResource
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
            "user" => User::find($this->user_id),
            "category" => Category::find($this->category_id)->name ?? "NULL",
            "subject" => Subject::find($this->subject_id)->name ?? "NULL",
            "need_help_with" => $this->need_help_with,
            "description" => $this->description,
            "deadline" => $this->deadline,
            "status" => $this->status,
            "executor_id" => $this->executor_id,
            "completion_date" => Carbon::parse($this->completion_date)->format("d.m.y H:i"),
            "created_at" => Carbon::parse($this->created_at)->format("d.m.y H:i"),
        ];
    }
}
