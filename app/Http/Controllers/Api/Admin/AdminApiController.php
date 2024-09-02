<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;

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
}
