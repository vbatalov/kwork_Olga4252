<script>
import axios from "axios";
import {PaperClipIcon} from '@heroicons/vue/20/solid'
import {CheckCircleIcon} from '@heroicons/vue/24/solid'
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import Textarea from "primevue/textarea";

export default {
    created() {
        this.order_id = this.$route.params.id;
        this.getOrder();
    },
    data() {
        return {
            order_id: null,
            data: null,
        }
    },
    methods: {
        updateOrder() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.patch(route("updateOrder", {
                    'id': this.order_id,
                    'description': this.data.order.description,
                })).then(r => {
                });
            });
        },
        getOrder() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getOrderById", {'id': this.order_id})).then(r => {
                    this.data = r.data;
                });
            });
        }
    },
    components: {
        PaperClipIcon, CheckCircleIcon, InputText, Button, Textarea
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
                       <Textarea class="w-full min-h-40" v-model="data.order.description"></Textarea>
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

        <div class="mb-8">
            <Button @click="updateOrder()" label="Редактировать заказ"/>
        </div>

        <div v-if="data != null">
            <p class="font-bold text-lg mb-4">
                Сообщения между пользователями
            </p>
            <ul role="list" class="space-y-6">

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
