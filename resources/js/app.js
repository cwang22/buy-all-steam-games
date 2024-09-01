import '../sass/app.scss';
import { createApp } from 'vue/dist/vue.esm-bundler';

const app = createApp();

import Home from './components/Home.vue';
app.component('home', Home);

app.mount('#app');