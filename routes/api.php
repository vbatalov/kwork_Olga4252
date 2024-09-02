<?php

use App\Http\Controllers\Api\Admin\AdminApiController;
use App\Http\Controllers\VK\VKSpecialistController;
use App\Http\Controllers\VK\VKStudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix("admin")->group(function () {
    Route::controller(AdminApiController::class)->group(function () {
        Route::post("getStats", "getStats")->name("getStats");
        Route::get("getUsers", "getUsers")->name("getUsers");
        Route::get("getOrders", "getOrders")->name("getOrders");
    });
});

Route::controller(VKStudentController::class)->group(function () {
    Route::post("vk_bot", "controller");
    Route::get("diagnostics", "diagnostics");
});

Route::controller(VKSpecialistController::class)->group(function () {
    Route::post("vk_bot_specialist", "controller");
    Route::post("orders_available", "orders_available")->name("orders_available");
    Route::get("diagnostics", "diagnostics");
});

Route::controller(\App\Http\Controllers\YooKassa::class)->group(function () {
    Route::post("payment_succeeded", "payment_succeeded");
});
