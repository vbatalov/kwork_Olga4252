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

        <div class="my-4">
            <div class="mb-2">
                Предметы специалиста
            </div>
            <MultiSelect v-model="selected_subjects" :options="subjects"
                         optionLabel="name" option-value="id" filter
                         optionGroupLabel="name" optionGroupChildren="subjects" display="chip"
                         placeholder="Выберите предметы" class="w-full">
                <template #optiongroup="slotProps">
                    <div class="flex items-center">
                        <div>{{ slotProps.option.name }}</div>
                    </div>
                </template>
            </MultiSelect>

            <div>
                <Button class="my-2 w-full" label="Сохранить категории" @click="saveCategories"/>
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
import MultiSelect from "primevue/multiselect";

import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';


export default {
    mounted() {
        this.currentUser = this.$route.params.id;
        this.getSubjects();
        this.getSpecialistCategories();
    },
    data() {
        return {
            selected_subjects: [],
            subjects: null,
            currentUser: null,
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
                axios.patch(route("updateSpecialistCategories"), {
                    'categories': this.selected_subjects,
                    'specialist_id': this.currentUser,
                }).then(() => {
                    this.sendToast('Предметы сохранены')
                });
            });
        },
        getSubjects() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getSubjects")).then(r => {
                    this.subjects = r.data.data
                });
            });
        },
        getSpecialistCategories() {
            axios.get('/sanctum/csrf-cookie').then(() => {
                axios.get(route("getSpecialistCategories", {'specialist_id': this.currentUser})).then(r => {
                    this.selected_subjects = r.data;
                });
            });
        },
    },
    components: {
        ChevronLeftIcon, ChevronRightIcon, Dialog, Button, Checkbox, InputText,
        Toast, ToastService, MultiSelect
    },
}

</script>

