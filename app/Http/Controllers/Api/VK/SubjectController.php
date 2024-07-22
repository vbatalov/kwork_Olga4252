<?php

namespace App\Http\Controllers\Api\VK;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Pagination\CursorPaginator;

class SubjectController extends Controller
{
    public static function getSubject($category_id, $page, $perPage = 6)
    {
        return Subject::where([
            "category_id" => $category_id
        ])->simplePaginate(perPage: $perPage, page: $page);
    }
}
