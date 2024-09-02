<template>
    <div>
        <div>
            <h3 class="text-base font-semibold leading-6 text-gray-900">
                Статистика сервиса
            </h3>
            <dl class="mt-5 grid grid-cols-1 divide-y divide-gray-200 overflow-hidden rounded-lg bg-white shadow md:grid-cols-3 md:divide-x md:divide-y-0">
                <div v-for="item in stats" :key="item.name">
                    <div class="px-4 py-5 sm:p-6">
                        <dt class="text-base font-normal text-gray-900">{{ item.name }}</dt>
                        <dd class="mt-1 flex items-baseline justify-between md:block lg:flex">
                            <div class="flex items-baseline text-2xl font-semibold text-indigo-600">
                                {{ item.count }}
                            </div>
                        </dd>
                    </div>
                </div>
            </dl>
        </div>
    </div>
</template>

<script>
import axios from "axios";
import { ArrowDownIcon, ArrowUpIcon } from '@heroicons/vue/20/solid'

export default {
    mounted() {
        this.getStats();
    },
    data() {
        return {
            stats: null,
        }
    },
    methods: {
        getStats() {
            axios.get('/sanctum/csrf-cookie').then(response => {
                axios.post(route("getStats")).then(r => {
                    this.stats = r.data
                    console.log(this.stats);
                });
            });
        },
    },
    components: {
        ArrowDownIcon, ArrowUpIcon
    },
}
</script>
