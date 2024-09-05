<script>
import axios from "axios";
import {PaperClipIcon} from '@heroicons/vue/20/solid'
import {CheckCircleIcon} from '@heroicons/vue/24/solid'

export default {
    created() {
        this.user_id = this.$route.params.id;
        this.getUser();
    },
    data() {
        return {
            user_id: null,
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
        getUser() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getUsers", {'user_id': this.user_id})).then(r => {
                    this.data = r.data.data;
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
                    Просмотр пользователя #{{ user_id }}

                </h1>
                <p class="mt-2 text-sm text-gray-700">
                    <router-link :to="'/users/'+user_id+'/logs'" class="px-2 py-1 bg-indigo-500 hover:bg-indigo-600 text-white">
                        Логи пользователя
                    </router-link>
                </p>
            </div>
        </div>


        <div class=" mt-6 border-t border-gray-100" v-if="data != null">
            <dl class="divide-y divide-gray-100">

                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        ID пользователя ВКонтакте
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.peer_id }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Имя
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.name }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Фамилия
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.surname }}
                    </dd>
                </div>
                <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                    <dt class="text-sm font-medium leading-6 text-gray-900">
                        Зарегистрирован
                    </dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0">
                        {{ data.created_at }}
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="my-4 flow-root p-4 bg-white" v-if="data.orders != null">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead>
                    <tr>
                        <th scope="col"
                            class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 sm:pl-0">
                            ID
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Статус
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Категория
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Предмет
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Тема
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Срок
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Исполнитель
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Создан
                        </th>
                        <th scope="col"
                            class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                            Сдан
                        </th>
                        <th scope="col" class="relative py-3 pl-3 pr-4 sm:pr-0">
                        </th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 ">
                    <tr v-for="order in data.orders">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                            {{ order.id }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.status }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.category }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.subject }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.need_help_with }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.deadline }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.executor_id }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.created_at }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            {{ order.completion_date }}
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                            <router-link :to="'/orders/'+order.id" class="text-indigo-600 hover:text-indigo-900">
                                Подробнее
                            </router-link>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</template>
