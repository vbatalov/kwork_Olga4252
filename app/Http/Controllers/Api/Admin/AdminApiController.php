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
            ])->get())
        ];
    }
}
