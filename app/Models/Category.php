<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        "name",
        "description",
    ];

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'category_id', 'id');
    }
}
