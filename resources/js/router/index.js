import HomePage from "../components/HomePage.vue";
import LoginPage from "../components/LoginPage.vue";

import OrdersPage from "../components/Order/OrdersPage.vue";
import OrderDetailPage from "../components/Order/OrderDetailPage.vue";

import UsersPage from "../components/User/UsersPage.vue";
import UserDetailPage from "../components/User/UserDeatilPage.vue";
import UserLogsPage from "../components/User/UserLogsPage.vue";

import CategoriesPage from "../components/Category/CategoriesPage.vue";
import CategorySettingsPage from '../components/Category/CategorySettingsPage.vue'

import SpecialistsPage from "../components/Specialist/SpecialistsPage.vue";

import NotFoundPage from "../components/NotFoundPage.vue";
import {createRouter, createWebHistory} from "vue-router";

const routes = [
    {
        path: '/',
        name: "Home",
        component: HomePage,
    },
    {
        path: '/login',
        component: LoginPage,
    },
    {
        path: '/users',
        component: UsersPage,
    },
    {
        path: '/users/:id',
        component: UserDetailPage,
    },
    {
        path: '/users/:id/logs',
        component: UserLogsPage,
    },

    {
        path: '/specialists',
        component: SpecialistsPage,
    },

    {
        path: '/orders',
        component: OrdersPage,
    },
    {
        path: '/orders/:id',
        component: OrderDetailPage,
    },
    {
        path: '/categories',
        component: CategoriesPage,
    },
    {
        path: '/categories/:id',
        component: CategorySettingsPage,
    },
    {
        path: '/:patchMatch(.*)*',
        component: NotFoundPage,
    }
]

const router = createRouter({
        history: createWebHistory(),
        routes,
    }
);

export default router;
