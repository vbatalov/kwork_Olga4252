<template>
    <div class="px-4 sm:px-6 lg:px-8 bg-white">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-gray-900 mt-8">Специалисты</h1>
                <p class="mt-0 text-sm text-gray-700">
                    Список специалистов бота
                </p>
            </div>

        </div>
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

        <div class="text-emerald-50">
            asd
        </div>
        <DataTable :value="users" paginator :rows="10" :rowsPerPageOptions="[5, 10, 20, 50]"
                   paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
                   currentPageReportTemplate="{first} to {last} of {totalRecords}">
            <template  #paginatorstart>
                <Button type="button" icon="pi pi-refresh" text/>
            </template>
            <template #paginatorend>
                <Button type="button" icon="pi pi-download" text/>
            </template>
            <Column  filter-field="id" field="id" header="id" style="width: 25%"></Column>
            <Column field="peer_id" header="PeerID" style="width: 25%"></Column>
            <Column field="name" header="Имя" style="width: 25%"></Column>
            <Column field="surname" header="Фамилия" style="width: 25%"></Column>
            <Column header="action">
                <template #body="{ data }">
                    <router-link :to="'specialists/' + data.id">
                        <Button icon="pi pi-search" severity="secondary" rounded></Button>

                    </router-link>
                </template>
            </Column>
        </DataTable>

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
import DataTable from 'primevue/datatable';
import Column from 'primevue/column';
import ColumnGroup from 'primevue/columngroup'; // optional
import Row from 'primevue/row'; // optional


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
                    this.users = r.data;
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
        Toast, ToastService, DataTable, Column, ColumnGroup, Row
    },
}

</script>

