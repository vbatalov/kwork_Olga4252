import './bootstrap';
import {createApp} from "vue";
import App from './components/App.vue'
import router from './router/index.js'
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import Theme from '@primevue/themes/nora';


const app = createApp(App);

app.use(PrimeVue, {
    theme: {
        preset: Theme
    }
})
    .use(ToastService)

app.use(router).mount("#app");
