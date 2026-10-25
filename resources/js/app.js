import '../css/app.css';
import { createApp } from 'vue';
import Chart from './components/Chart.vue';

const el = document.getElementById('trend-chart');

if (el) {
    createApp(Chart, { labels: JSON.parse(el.dataset.labels) }).mount(el);
}
