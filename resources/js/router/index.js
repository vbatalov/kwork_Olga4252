import HomePage from "../components/HomePage.vue";
import LoginPage from "../components/LoginPage.vue";
import OrdersPage from "../components/Order/OrdersPage.vue";
import OrderDetailPage from "../components/Order/OrderDetailPage.vue";
import UsersPage from "../components/UsersPage.vue";
import CategoriesPage from "../components/Category/CategoriesPage.vue";
import CategorySettingsPage from '../components/Category/CategorySettingsPage.vue'
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
