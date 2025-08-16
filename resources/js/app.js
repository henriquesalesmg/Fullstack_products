import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import PrimeVue from 'primevue/config';
import 'primeicons/primeicons.css';

const app = createApp(App);
app.use(router);
app.use(PrimeVue);
app.mount('#app');
