<template>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-gray-900">Специалисты</h1>
                <p class="mt-2 text-sm text-gray-700">
                    Список специалистов бота
                </p>
            </div>

        </div>
        <div class="mt-8 flow-root" v-if="users != null">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead>
                        <tr>
                            <th scope="col"
                                class="py-3 pl-4 pr-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500 sm:pl-0">
                                ID VK
                            </th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Имя
                            </th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Фамилия
                            </th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Процент
                            </th>
                            <th scope="col"
                                class="px-3 py-3 text-left text-xs font-medium uppercase tracking-wide text-gray-500">
                                Дата создания
                            </th>
                            <th scope="col" class="relative py-3 pl-3 pr-4 sm:pr-0">
                            </th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 ">
                        <tr v-for="user in users">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-0">
                                {{ user.peer_id }}
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user.name }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user.surname }}</td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <div class="flex">
                                    <InputText type="text" class="w-16 text-center" v-model="user.percent"/>
                                    <Button @click="savePercent(user.id, user.percent)" label="" icon="pi pi-check"/>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ user.created_at }}</td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                <Button :raised="true" class="text-xs" label="Категории"
                                        @click="getSpecialistCategories(user.id)"/>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div>
                <Dialog v-model:visible="modal_visible" modal header="Управление специалистом"
                        :style="{ width: '30rem' }"
                        :breakpoints="{ '1199px': '75vw', '575px': '90vw' }">
                    <div class="">
                        <div class="card flex justify-start">
                            <div class="flex flex-col gap-4">
                                <div v-for="category of categories" :key="category.id" class="flex items-center">
                                    <Checkbox v-model="selected_categories" :inputId="category.id.toString()"
                                              name="category"
                                              :value="category.id"/>
                                    <label class="px-2" :for="category.name">{{ category.name }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="my-4">
                            <Button @click="saveCategories()">Сохранить категории</Button>
                        </div>
                    </div>
                </Dialog>
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
        <div class="mt-4" v-if="users != null">
            <div class="flex justify-start">
                <button type="button" @click="prevPage" v-if="page > 1"
                        class="relative inline-flex items-center rounded-l-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                    <ChevronLeftIcon class="h-5 w-5" aria-hidden="true"/>
                </button>
                <button type="button" @click="nextPage"
                        class="relative -ml-px inline-flex items-center rounded-r-md bg-white px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-10">
                    <ChevronRightIcon class="h-5 w-5" aria-hidden="true"/>
                </button>
            </div>


        </div>
    </div>

    <div>
        <Toast position="bottom-right"/>
    </div>

</template>
<script>
import axios from "axios";
import Dialog from 'primevue/dialog';
import Button from "primevue/button";
import {ChevronLeftIcon, ChevronRightIcon} from '@heroicons/vue/20/solid'
import Checkbox from 'primevue/checkbox';
import InputText from 'primevue/inputtext';
import 'primeicons/primeicons.css'

import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';


export default {
    mounted() {
        this.getSpecialists();
        this.getCategories();
    },
    data() {
        return {
            selected_categories: null,
            categories: null,
            currentUser: null,
            modal_visible: false,
            users: null,
            links: null,
            page: 1
        }
    },
    methods: {
        sendToast(detail = null) {
            this.$toast.add({
                severity: 'success',
                summary: 'Успешно',
                detail: detail,
                life: 3000
            })
        },
        saveCategories() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.put(route("updateSpecialistCategories", {
                    'categories': this.selected_categories,
                    'specialist_id': this.currentUser
                })).then(r => {
                    this.modal_visible = false;
                    this.sendToast('')

                });
            });
        },
        savePercent(id, percent) {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.patch(route("updateSpecialistPercent", {
                    'id': id,
                    'percent': percent,
                })).then(() => {
                    this.sendToast('')
                });
            });
        },
        getSpecialists() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getSpecialists", {'page': this.page})).then(r => {
                    this.users = r.data.data;
                    this.links = r.data.links;
                });
            });
        },
        getSpecialistCategories(id) {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getSpecialistCategories", {'specialist_id': id})).then(r => {
                    this.selected_categories = r.data;
                    this.currentUser = id;
                    this.modal_visible = true;
                });
            });
        },
        getCategories() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getCategories")).then(r => {
                    this.categories = r.data.data;
                });
            });
        },
        nextPage() {
            this.page = this.page + 1;
            this.users = null;
            this.getSpecialists()
        },
        prevPage() {
            this.page = this.page - 1;
            this.users = null;
            this.getSpecialists()
        }
    },
    components: {
        ChevronLeftIcon, ChevronRightIcon, Dialog, Button, Checkbox, InputText,
        Toast, ToastService
    },
}

</script>

