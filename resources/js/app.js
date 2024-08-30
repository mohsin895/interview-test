import "../css/app.css"
import {createApp} from "vue";
import router from "@/routes/index.js";
import App from "./App.vue"
import PrimeVue from 'primevue/config';
import Aura from '@primevue/themes/aura';
import Button from "primevue/button"

    createApp(App)
    .use(router)
    .component('Button', Button)
    .use(PrimeVue, {
        theme: {
            preset: Aura
        }
    })
    .mount("#app")


