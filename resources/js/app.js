import '../css/app.css';
import { createApp } from 'vue';
import Chart from './components/Chart.vue';

// The server-rendered "updated x ago" goes stale while the page is cached, so recompute it here.
const units = [['year', 31536000], ['month', 2592000], ['week', 604800], ['day', 86400], ['hour', 3600], ['minute', 60], ['second', 1]];
const relative = new Intl.RelativeTimeFormat(document.documentElement.lang);

document.querySelectorAll('time[datetime]').forEach((time) => {
    const seconds = Math.max(0, (Date.now() - Date.parse(time.dateTime)) / 1000);
    const [unit, size] = units.find(([, size]) => seconds >= size) ?? units.at(-1);
    time.textContent = relative.format(-Math.floor(seconds / size), unit);
});

const el = document.getElementById('trend-chart');

if (el) {
    createApp(Chart, { labels: JSON.parse(el.dataset.labels) }).mount(el);
}
