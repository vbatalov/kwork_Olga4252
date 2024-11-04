@if(count($orders) == 0)
Заказов в работе нет
@else
Заказы в работе
@endif

@foreach($orders as $order)
Заказ № {{$order->id}}
Категория: {{$order->category->name}}
Предмет: {{$order->subject->name}}
Необходима помощь: {{$order->need_help_with}}
Срок: {{$order->deadline}}
–   –   –   –   –   –   –   –   –   –   –
@endforeach
