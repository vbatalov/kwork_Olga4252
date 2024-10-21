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
        Route::post("stats", "getStats")->name("getStats");
        Route::get("users", "getUsers")->name("getUsers");
        Route::get("user_logs", "getUserLogs")->name("getUserLogs");

        Route::get("orders", "getOrders")->name("getOrders");
        Route::get("order", "getOrder")->name("getOrderById");
        Route::patch("order", "updateOrder")->name("updateOrder");

        Route::get("subjects", "getSubjects")->name("getSubjects");

        Route::get("categories", "getCategories")->name("getCategories");
        Route::get("category", "getCategory")->name("get-category");
        Route::put("category", "updateOrCreateCategory")->name("save-category");

        Route::get("specialists", "getSpecialists")->name("getSpecialists");
        Route::get("specialist-categories", "getSpecialistCategories")->name("getSpecialistCategories");
        Route::put("specialist-categories", "updateSpecialistCategories")->name("updateSpecialistCategories");
        Route::patch("specialist-percent", "updateSpecialistPercent")->name("updateSpecialistPercent");

        Route::put("createSubject", "createSubject")->name("createSubject");
        Route::put("updateSubject", "updateSubject")->name("updateSubject");
        Route::delete("deleteSubject", "deleteSubject")->name("deleteSubject");
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
