import '../sass/app.scss';
import { createApp } from 'vue/dist/vue.esm-bundler';

const app = createApp();

import Chart from './components/Chart.vue';
app.component('chart', Chart);

app.mount('#app');