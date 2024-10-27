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

        <DataTable :value="users" paginator :rows="10" :rowsPerPageOptions="[5, 10, 20, 50]"
                   v-model:filters="filters"
                   filterDisplay="row"
                   paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink  NextPageLink LastPageLink"
                   currentPageReportTemplate="{first} to {last} of {totalRecords}">

            <template #header>
                <div class="flex justify-end">
                    <InputText v-model="filters['global'].value" placeholder="Поиск"/>
                </div>
            </template>

            <Column class="text-sm" filter-field="peer_id" field="peer_id" header="Идентификатор ВК"></Column>
            <Column class="text-sm" field="name" header="Имя" style=""></Column>
            <Column class="text-sm" field="surname" header="Фамилия" style=""></Column>
            <Column class="text-sm" header="Процент">
                <template #body="{ data }">
                    <InputText class="w-24" v-model="data.percent" />
                    <Button @click="savePercent(data.id, data.percent)">
                        SAVE
                    </Button>
                </template>
            </Column>
            <Column class="text-sm" header="">
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
import {FilterMatchMode} from '@primevue/core/api';


export default {
    mounted() {
        this.getSpecialists();
        // this.getCategories();
    },
    data() {
        return {
            selected_categories: null,
            categories: null,
            currentUser: null,
            users: null,
            filters: {
                global: {value: null, matchMode: FilterMatchMode.CONTAINS},
            }
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
        getSpecialists() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getSpecialists", {'page': this.page})).then(r => {
                    this.users = r.data;
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
    },
    components: {
        ChevronLeftIcon, ChevronRightIcon, Dialog, Button, Checkbox, InputText,
        Toast, ToastService, DataTable, Column, ColumnGroup, Row
    },
}

</script>

