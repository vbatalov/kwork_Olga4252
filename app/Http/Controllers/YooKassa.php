<?php

namespace App\Http\Controllers;

use App\Http\Controllers\VK\VKSpecialistController;
use App\Http\Controllers\VK\VKStudentController;
use App\Models\Order;
use App\Models\Response;
use App\Models\Specialist;
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

    public function create($amount, $order_id)
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
                        "order_id" => "$order_id",
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

    public function payment_succeeded(Request $request)
    {
        $object = $request->get('object');
        $metadata = $object['metadata'];

        $order = Order::findOrFail($metadata['order_id']);
        Order::setStatus($order->id, "progress");

        $response = Response::where(['order_id' => $order->id])->firstOrFail();
        $user = User::findOrFail($order->user_id);
        $specialist = Specialist::where(["id" => $response->executor_id])->firstOrFail();

        $student_vk = new VKStudentController();
        $student_vk->bot->msg("Заказ $order->id оплачен.")->send($user->peer_id);

        $specialist_vk = new VKSpecialistController();
        $specialist_vk->bot->msg("Заказчик оплатил заказ № $order->id")->send($specialist->peer_id);

        \App\Models\Log::add(user_id: $order->user_id, action: "Заказ оплачен", class: Order::class, action_id: $order->id);


        print_r($object);
    }

}
