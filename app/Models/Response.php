<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Response extends Model
{
    protected $fillable =
        [
            "order_id",
            "executor_id",
            "price",
            "note",
            "status",
        ];

    public static function addResponse($executor_id, $order_id, $price): void
    {
        Response::updateOrCreate(
            [
                "order_id" => $order_id,
                "executor_id" => $executor_id
            ],
            [
                "price" => $price
            ]);
    }

    public static function addNoteToResponse($executor_id, $order_id, $note): void
    {
        Response::where(
            [
                "order_id" => $order_id,
                "executor_id" => $executor_id
            ],
        )->update(
            [
                "note" => $note
            ]);
    }

    public static function setStatus($executor_id, $order_id, $status): void
    {
        Response::where(
            [
                "order_id" => $order_id,
                "executor_id" => $executor_id
            ],
        )->update(
            [
                "status" => $status
            ]);
    }
}
