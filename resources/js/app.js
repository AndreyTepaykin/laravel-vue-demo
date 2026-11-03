import './bootstrap';
import { createApp } from 'vue';
import App from './argon/App.vue';
import store from './argon/store';
import router from './argon/router';
import './argon/assets/css/nucleo-icons.css';
import './argon/assets/css/nucleo-svg.css';
import ArgonDashboard from './argon/argon-dashboard';

const app = createApp(App);

app.use(store);
app.use(router);
app.use(ArgonDashboard);
app.config.globalProperties.$isDemo = Number(import.meta.env.VITE_IS_DEMO ?? 0);

app.mount('#app');
