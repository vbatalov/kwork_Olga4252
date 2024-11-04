У вас появился новый отклик на заказ.

Заказ № {{$order->id}}

Категория {{$order->category->name}}
Предмет {{$order->subject->name}}
Тема {{$order->need_help_with}}

Цена: {{$order->response($order->id, $specialist->id)->price}}
Комментарий: {{$order->response($order->id, $specialist->id)->note}}
