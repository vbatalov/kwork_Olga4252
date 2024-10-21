import './bootstrap';
import {createApp} from "vue";
import App from './components/App.vue'
import router from './router/index.js'
import PrimeVue from 'primevue/config';
import ToastService from 'primevue/toastservice';
import Nora from '@primevue/themes/nora';
import Aura from '@primevue/themes/aura';


const app = createApp(App);

app.use(PrimeVue, {
    theme: {
        preset: Aura
    }
})
    .use(ToastService)

app.use(router).mount("#app");
