@php
    if ($order->status == "pending") {
        $status = "Ждет откликов специалистов";
    }  elseif ($order->status == "draft") {
        $status = "Черновик";
    } elseif ($order->status == "wait_payment") {
        $status = "Ожидает платежа";
    } elseif ($order->status == "progress") {
        $status = "В работе";
    } elseif ($order->status == "finish") {
        $status = "Завершен";
    } else {
        $status = "Статус не установлен на сервере. (MySQL: $order->status)";
    }
@endphp
📋 Заказ № {{$order->id}}
Категория: {{$order->category->name}}
Предмет: {{$order->subject->name}}
Необоходима помощь: {{$order->need_help_with}}
Сроки: {{$order->deadline}}

Описание: {{$order->description ?? 'Отсутствует'}}

Статус заказа: {{$status}}
