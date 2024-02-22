<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $fillable = [
        "user_id",
        "action",
        "action_id",
        "class",
        "author",
    ];

    /** Добавить Log в базу данных */
    public static function add
    (
        $user_id,
        string $action,
        string $class,
        int $action_id = null,
        string $author = "system"
    )
    {
        Log::create([
            "user_id" => $user_id,
            "action" => $action,
            "action_id" => $action_id,
            "class" => $class,
            "author" => $author,
        ]);
    }
}
