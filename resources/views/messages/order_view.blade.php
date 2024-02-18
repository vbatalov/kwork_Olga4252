@php
    if ($order->status == "pending") {
        $status = "Ждет откликов специалистов";
    } elseif ($order->$status == "in_work") {
        $status = "В работе";
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
