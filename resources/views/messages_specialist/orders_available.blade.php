@if(count($orders) == 0)
Заказов больше нет. Вернитесь на предыдущую страницу.
@else
Доступные заказы:
@endif

@foreach($orders as $order)
Заказ № {{$order->id}}
Категория: {{$order->category->name}}
Предмет: {{$order->subject->name}}
Необходима помощь: {{$order->need_help_with}}
Срок: {{$order->deadline}}
Опубликовано: {{\Carbon\Carbon::parse($order->created_at)->format("d.m.Y H:s")}}
–   –   –   –   –   –   –   –   –   –   –
@endforeach


