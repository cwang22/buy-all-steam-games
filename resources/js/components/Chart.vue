<template>
    <div>
        <div class="float-right">
            <button v-for="button in buttons" :key="button" type="button" class="btn ml-2" :class="buttonClass(button)" @click="update(button)">{{button}}</button>
        </div>

        <div class="wrapper">
            <Line id="chart" :data="chartData" :options="chartOptions" />
        </div>
    </div>

</template>
<script>
    import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend,
    TimeScale,
    } from 'chart.js'
    import { Line } from 'vue-chartjs'
    import 'chartjs-adapter-date-fns'

    ChartJS.register(
        CategoryScale,
        LinearScale,
        PointElement,
        LineElement,
        Title,
        Tooltip,
        Legend,
        TimeScale,
    )

    export default {
        props: ['records'],
        components: {
            Line
        },
        data() {
            return {
                currentButton: 'All',
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                        text: 'Total Price (USD)'
                    },
                    tooltips: {
                        mode: 'index',
                        intersect: false
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    },
                    scales: {
                        x: {
                            type: 'time'
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            }
        },
        computed: {
            buttons() {
                return ['All', 'Year', 'Month']
            },
            chartData() {

                const filterData = (data) => {
                    if (this.currentButton === 'Year') {
                        const oneYearAgo = new Date();
                        oneYearAgo.setFullYear(oneYearAgo.getFullYear() - 1);
                        return data.filter(item => item.x >= oneYearAgo);
                    }
                    if (this.currentButton === 'Month') {
                        const oneMonthAgo = new Date();
                        oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
                        return data.filter(item => item.x >= oneMonthAgo);
                    }
                    return data;
                };

                const sale = filterData(this.records.map(record => {
                    return {
                        x: new Date(record.created_at),
                        y: record.sale
                    }
                }));

                const original = filterData(this.records.map(record => {
                    return {
                        x: new Date(record.created_at),
                        y: record.original
                    }
                }));
                
                return {
                    datasets: [
                        {
                            label: 'Sale Price',
                            data: sale,
                            borderColor: '#0074D9',
                            cubicInterpolationMode: 'monotone',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointHitRadius: 10,
                            pointBackgroundColor: '#0074D9',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#0074D9'
                        },
                        {
                            label: 'Original Price',
                            data: original,
                            borderColor: '#FF4136',

                            cubicInterpolationMode: 'monotone',
                            pointRadius: 3,
                            pointHoverRadius: 5,
                            pointHitRadius: 10,
                            pointBackgroundColor: '#FF4136',
                            pointBorderColor: '#fff',
                            pointHoverBackgroundColor: '#fff',
                            pointHoverBorderColor: '#FF4136'
                        }
                    ]
                }
            }
        },
        methods: {
            buttonClass(button) {
                return this.currentButton === button ? 'btn-dark' : 'btn-outline-dark'
            },
            update(button) {
                if (button === this.currentButton) return
                this.currentButton = button
            }
        }
    }
</script>
<style scoped>
    .wrapper {
        height: 50vh;
    }
</style>