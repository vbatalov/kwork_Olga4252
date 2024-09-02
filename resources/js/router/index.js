import HomePage from "../components/HomePage.vue";
import LoginPage from "../components/LoginPage.vue";
import OrdersPage from "../components/Order/OrdersPage.vue";
import UsersPage from "../components/UsersPage.vue";
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
        path: '/orders',
        component: OrdersPage,
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
