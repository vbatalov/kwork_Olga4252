<?php

namespace Tests\Http\Controllers\Api\VK;

use App\Http\Controllers\Api\VK\SubjectController;
use Tests\TestCase;

class SubjectControllerTest extends TestCase
{

    public function testGetParent()
    {
        $items = SubjectController::getSubject(1, 2,5 );
        dump($items->onFirstPage());
    }
}
