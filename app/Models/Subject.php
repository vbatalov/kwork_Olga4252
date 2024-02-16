<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected array $fillable = [
        "category_id",
        "name",
        "description",
    ];

}
