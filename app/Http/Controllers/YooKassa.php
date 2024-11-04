<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VK\VKSpecialistController;
use App\Http\Controllers\VK\VKStudentController;
use App\Models\Order;
use App\Models\Response;
use App\Models\Specialist;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use YooKassa\Client;


class YooKassa extends Controller
{
    public Client $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setAuth(login: config("app.YOOKASSA_ID"), password: config("app.YOOKASSA_API"));
    }

    public function payment_succeeded(Request $request)
    {
        $object = $request->get('object');
        $metadata = $object['metadata'];

        $order = Order::findOrFail($metadata['order_id']);
        Order::setStatus($order->id, "progress");

        // Модель откликов
        $response = Response::where(['id' => $metadata['response_id']])->firstOrFail();

        $user = User::findOrFail($order->user_id);
        $specialist = Specialist::where(["id" => $response->executor_id])->firstOrFail();
        Order::setExecutor($order->id, $specialist->id);

        $student_vk = new VKStudentController();
        $student_text = view('messages.success_payed_order_student', ['order_id' => $order->id]);
        $student_vk->bot->msg($student_text)->send($user->peer_id);

        $specialist_vk = new VKSpecialistController();
        $specialist_text = view('messages_specialist.order_payed', ['order_id' => $order->id]);
        $specialist_vk->bot->msg($specialist_text)->send($specialist->peer_id);

        \App\Models\Log::add(user_id: $order->user_id, action: "Заказ оплачен", class: Order::class, action_id: $order->id);

        Transaction::create([
            "order_id" => $order->id,
            "executor_id" => $specialist->id,
        ]);
    }

    public function create($amount, $order_id, $response_id)
    {
        try {
            $payment = $this->client->createPayment(
                array(
                    "amount" => array(
                        "value" => doubleval($amount),
                        "currency" => "RUB",
                    ),
                    "confirmation" => array(
                        "type" => "redirect",
                        "return_url" => "https://vk.com/",
                    ),
                    "capture" => true,
                    "description" => "Заказ № $order_id",
                    "metadata" => array(
                        "order_id" => $order_id,
                        "response_id" => $response_id
                    )
                ),
                uniqid("", true)
            );

            return ($payment->confirmation->getConfirmationUrl());
        } catch (\Exception $e) {
            Log::error($e);
        }
        return false;
    }

}
