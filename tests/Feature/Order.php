<?php

namespace Tests\Feature;

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

    public function test_specialistCategories()
    {
        $r = \App\Models\Order::get()
            ->whereIn('category_id', [2])
            ->where("status", "pending");

        dd($r);
    }

    public function test_specialistCategoryUri()
    {
        $o = \App\Models\Order::findOrFail(4);
        $att = $o->attachments;

        dd($att);
    }
}
