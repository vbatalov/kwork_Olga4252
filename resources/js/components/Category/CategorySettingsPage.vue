<script>
import axios from "axios";
import router from "../../router/index.js";
import {Menu, MenuButton, MenuItem, MenuItems} from '@headlessui/vue'
import {BarsArrowUpIcon, ChevronDownIcon, DocumentTextIcon} from '@heroicons/vue/20/solid'

import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import { useToast } from 'primevue/usetoast';

export default {
    created() {
        this.showCategory(router.currentRoute.value.params.id)
    },
    data() {
        return {
            category: null,
            subjects: null,
            isOpen: false,
            category_name: null,
        }
    },
    methods: {
        showCategory(category_id) {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("get-category", {'category_id': category_id})).then(r => {
                    this.subjects = r.data.subjects;
                    this.category = r.data.category;
                    this.category_name = this.category.name;
                });
            });
        },

        saveCategory(category_id) {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.put(route("save-category", {
                    "category_id": category_id,
                    "category_name": this.category_name,
                })).then(r => {

                });
            });
        }
    },
    components: {
        Menu, MenuButton, MenuItems, MenuItem, ChevronDownIcon,
        BarsArrowUpIcon, DocumentTextIcon,
        Toast, ToastService
    }
}
</script>

<template>
    <div>
        <Toast/>

        <div class="px-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center">
                <div class="sm:flex-auto">
                    <h1 class="text-base font-semibold leading-6 text-gray-900">Управление категорией предметами</h1>
                    <p class="mt-2 text-sm text-gray-700">
                        Измените наименование категории или добавьте, удалите, измените предметы
                    </p>
                </div>
                <div>
                    <button type="button"
                            class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">

                        Добавить предмет
                    </button>
                </div>
            </div>

            <div class="max-w-xl">
                <div class="mt-2 flex rounded-md shadow-sm">
                    <div class="relative flex flex-grow items-stretch focus-within:z-10">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <DocumentTextIcon class="h-5 w-5 text-gray-400" aria-hidden="true"/>
                        </div>
                        <input type="text" v-model="category_name"
                               class="block w-full rounded-none rounded-l-md border-0 py-1.5 pl-10 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                               placeholder="Укажите наименование"/>
                    </div>
                    <button type="button" @click="saveCategory(category.id)"
                            class="relative -ml-px inline-flex items-center gap-x-1.5 rounded-r-md px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        <BarsArrowUpIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                        Сохранить
                    </button>
                </div>
            </div>

            <div class="mt-8 flow-root" v-if="subjects != null">
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
                                    class=" w-full px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                    Наименование
                                </th>

                                <th scope="col" class="relative py-3 pl-3 pr-4 sm:pr-0">
                                </th>
                                <th scope="col" class="relative py-3 pl-3 pr-4 sm:pr-0">
                                </th>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 ">
                            <tr v-for="subject in subjects">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                    {{ subject.id }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    <input type="text" v-model="subject.name"
                                           class="block w-full rounded-none rounded-l-md border-0 py-1.5 pl-2 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                           placeholder="Укажите наименование"/>
                                </td>
                                <td>
                                    <button type="button"
                                            class="relative -ml-px inline-flex items-center gap-x-1.5 px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                        <BarsArrowUpIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                        Сохранить
                                    </button>
                                </td>

                                <td>
                                    <button type="button"
                                            class="relative -ml-px inline-flex items-center gap-x-1.5  px-3 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-red-50">
                                        <BarsArrowUpIcon class="-ml-0.5 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                        Удалить
                                    </button>
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
    </div>


</template>

<style scoped>

</style>
