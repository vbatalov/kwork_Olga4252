<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class Order extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $order = new \App\Models\Order();
        $orders = $order->getAvailableOrders();
        dd($orders);

    }
}
