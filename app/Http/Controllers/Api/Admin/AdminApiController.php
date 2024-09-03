<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\Order;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class AdminApiController extends Controller
{
    public function getStats()
    {
        return [
            [
                "name" => "Пользователи",
                "count" => User::count(),
            ],
            [
                "name" => "Заказы",
                "count" => Order::count(),
            ],
            [
                "name" => "Сумма заказов",
                "count" => rand(1000, 100000) . " RUB"
            ],
        ];
    }

    public function getUsers()
    {
        return UserResource::collection(User::paginate(10));
    }

    public function getOrders()
    {
        return OrderResource::collection(Order::paginate(10));
    }

    public function getCategories()
    {
        return CategoryResource::collection(Category::all());
    }

    public function getCategory(Request $request)
    {
        $category_id = $request->get("category_id");
        return [
            "category" => CategoryResource::make(Category::find($category_id)),
            "subjects" => SubjectResource::collection(Subject::where([
                "category_id" => $category_id
            ])->orderBy("sort")->get())
        ];
    }

    public function updateOrCreateCategory(Request $request)
    {

        $category = Category::find($request->input("category_id"));

        if ($category) {
            $category->update([
                "name" => $request->input("category_name")
            ]);
            return response()->json([
                "status" => "updated"
            ]);
        } else {
            Category::create([
                "name" => $request->input("category_name")
            ]);
            return response()->json([
                "status" => "created"
            ], 201);
        }
    }

    public function createSubject(Request $request)
    {

        $category_id = $request->input("category_id");
        $name = $request->input("name");

        Subject::create([
            "name" => $name,
            "category_id" => $category_id
        ]);

        return response()->json([
            "status" => "created"
        ], 201);

    }

    public function updateSubject(Request $request)
    {
        $subject_id = $request->input("subject_id");
        $name = $request->input("name");
        $sort = $request->integer("sort");

        Subject::find($subject_id)->update([
            "name" => $name,
            "sort" => $sort
        ]);

        return response()->json([
            "status" => "updated"
        ], 200);

    }

    public function deleteSubject(Request $request)
    {
        $id = $request->input("subject_id");

        Subject::find($id)->delete();

        return response()->json([
            "status" => "deleted"
        ], 200);

    }

}
