import './bootstrap';
import {createApp} from "vue";
import App from './components/App.vue'
import router from './router/index.js'


const app = createApp(App);
app.use(router)
    .mount("#app");
