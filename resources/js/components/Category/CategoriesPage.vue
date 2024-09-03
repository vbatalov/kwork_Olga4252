<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-gray-900">Управление категориями и предметами</h1>
                <p class="mt-2 text-sm text-gray-700">
                    Быстрый доступ к любой категории и возможность изменения дочерних предметов
                </p>
            </div>

        </div>
        <div class="mt-8 flow-root" v-if="categories != null">
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
                                Наименование
                            </th>

                            <th scope="col" class="relative py-3 pl-3 pr-4 sm:pr-0">
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 ">
                        <tr v-for="category in categories">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                {{ category.id }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                {{ category.name }}
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                <router-link :to="'/categories/'+category.id" class="text-indigo-600 hover:text-indigo-900">
                                    Подробнее
                                </router-link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div v-else class="flex justify-center md:justify-start my-6">
            <div role="status">
                <svg aria-hidden="true" class="w-8 h-8 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                     viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                        fill="currentColor"/>
                    <path
                        d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                        fill="currentFill"/>
                </svg>
                <span class="sr-only">Loading...</span>
            </div>
        </div>

    </div>

</template>
<script>
import axios from "axios";
import {ChevronLeftIcon, ChevronRightIcon} from '@heroicons/vue/20/solid'

export default {
    mounted() {
        this.getCategories();

    },
    data() {
        return {
            categories: null,
            category: null,
        }
    },
    methods: {
        getCategories() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getCategories")).then(r => {
                    this.categories = r.data.data;

                });
            });
        },
    },
    components: {ChevronLeftIcon, ChevronRightIcon},
}

</script>

