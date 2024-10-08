<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class SpecialistFavoriteSubject extends Model
{

    /**
     *  Модель не используется. Вероятно это избранный предмет специалиста.
     */

    protected $fillable =
        [
            "user_id",
            "favorite_subject_id",
        ];


    public function create(Specialist $specialist, Subject $subject)
    {
        $this->updateOrCreate(
            [
                "user_id" => $specialist->id,
                "favorite_subject_id" => $subject->id
            ]
        );
    }
}
