<?php

namespace Models;

use App\Models\Specialist;
use App\Models\SpecialistFavoriteSubject;
use App\Models\Subject;
use Tests\TestCase;

class SpecialistFavoriteSubjectTest extends TestCase
{
    public function testCreate()
    {
        $specialist = Specialist::updateOrCreate([
            "peer_id" => 120637023
        ]);

        $model = new SpecialistFavoriteSubject();
        $model->create(specialist: $specialist,subject: Subject::find(5));

        dd($model);
    }
}
