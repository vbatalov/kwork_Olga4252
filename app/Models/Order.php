<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected array $fillable = [
        "user_id",
        "category_id",
        "subject_id",
        "description",
        "deadline",
        "status",
        "executor_id",
        "completion_date",
    ];
}
