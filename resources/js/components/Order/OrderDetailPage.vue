<script>
import axios from "axios";
import {PaperClipIcon} from '@heroicons/vue/20/solid'
import {CheckCircleIcon} from '@heroicons/vue/24/solid'

export default {
    created() {
        this.order_id = this.$route.params.id;
        this.getOrder();
    },
    data() {
        return {
            order_id: null,
            data: null,

            activity: [
                {id: 1, type: 'created', person: {name: 'Chelsea Hagon'}, date: '7d ago', dateTime: '2023-01-23T10:32'},
                {id: 2, type: 'edited', person: {name: 'Chelsea Hagon'}, date: '6d ago', dateTime: '2023-01-23T11:03'},
                {id: 3, type: 'sent', person: {name: 'Chelsea Hagon'}, date: '6d ago', dateTime: '2023-01-23T11:24'},
                {
                    id: 4,
                    type: 'commented',
                    person: {
                        name: 'Chelsea Hagon',
                        imageUrl:
                            'https://images.unsplash.com/photo-1550525811-e5869dd03032?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80',
                    },
                    comment: 'Called client, they reassured me the invoice would be paid by the 25th.',
                    date: '3d ago',
                    dateTime: '2023-01-23T15:56',
                },
                {id: 5, type: 'viewed', person: {name: 'Alex Curren'}, date: '2d ago', dateTime: '2023-01-24T09:12'},
                {id: 6, type: 'paid', person: {name: 'Alex Curren'}, date: '1d ago', dateTime: '2023-01-24T09:20'},
            ]

        }
    },
    methods: {
        getOrder() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getOrderById", {'id': this.order_id})).then(r => {
                    this.data = r.data;
                });
            });
        }
    },
    components: {
        PaperClipIcon, CheckCircleIcon
    }
}
</script>

<template>
    <div class="p-4 sm:px-6 lg:px-8 bg-white">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-gray-900">
                    Просмотр заказа #{{ order_id }}
                </h1>
                <p class="mt-2 text-sm text-gray-700">

                </p>
            </div>
        </div>


        <div class=" mt-6 border-t border-gray-100" v-if="data != null">
            <dl class="divide-y divide-gray-100">

                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Статус заказа
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.status }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Категория
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.category }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Предмет
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.subject }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Необходима помощь
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.need_help_with }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Описание заказчика
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.description }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Дата создания
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.created_at }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Deadline
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.deadline }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        ID исполнителя
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.order.executor_id }}
                    </dd>
                </div>

                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Вложения</dt>
                    <dd class="mt-2 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                        <ul role="list" class="divide-y divide-gray-100 rounded-md border border-gray-200">
                            <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm leading-6">
                                <div class="flex w-0 flex-1 items-center">
                                    <PaperClipIcon class="h-5 w-5 flex-shrink-0 text-gray-400" aria-hidden="true"/>
                                    <div class="ml-4 flex min-w-0 flex-1 gap-2">
                                        <span class="truncate font-medium">resume_back_end_developer.pdf</span>
                                        <span class="flex-shrink-0 text-gray-400">2.4mb</span>
                                    </div>
                                </div>
                                <div class="ml-4 flex-shrink-0">
                                    <a href="#"
                                       class="font-medium text-indigo-600 hover:text-indigo-500">Download</a>
                                </div>
                            </li>
                        </ul>
                    </dd>
                </div>
            </dl>
        </div>

        <div v-if="data != null">
            <p class="font-bold text-lg mb-4">
                Сообщения между пользователями
            </p>
            <ul role="list" class="space-y-6" >

                <li v-for="item in data.messages" class="relative flex gap-x-4">
                    <div
                        class="', 'absolute left-0 top-0 flex w-6 justify-center">
                        <div class="w-px bg-gray-200"/>
                    </div>

                    <div class="flex-auto">
                        <div class="flex justify-between gap-x-2">
                            <div class="py-0.5 text-xs leading-5 text-gray-500">
                                <span v-if="item.sender === 'user'"
                                      class="font-medium text-gray-900">Пользователь</span>
                                <span v-if="item.sender === 'specialist'"
                                      class="font-medium text-gray-900">Специалист</span>
                                написал
                            </div>
                            <time
                                class="flex-none py-0.5 text-xs leading-5 text-gray-500">
                                {{ item.diff }}
                            </time>
                        </div>
                        <p class="text-sm leading-6 text-gray-500">
                            {{ item.message }}
                        </p>
                    </div>
                </li>
            </ul>
        </div>


    </div>
</template>
