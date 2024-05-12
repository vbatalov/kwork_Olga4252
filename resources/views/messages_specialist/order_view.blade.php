📋 Заказ № {{$order->id}}
Категория: {{$order->category->name}}
Предмет: {{$order->subject->name}}
Необоходима помощь: {{$order->need_help_with}}
Сроки: {{$order->deadline}}

Описание: {{$order->description ?? 'Отсутствует'}}
