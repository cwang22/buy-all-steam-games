<template>
    <div>
      <div v-if="!records.length" class="mt-5 text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="d-none">Loading...</span>
        </div>
      </div>
      <div v-else>
        <div class="float-right">
          <button v-for="button in buttons" :key="button" type="button" class="btn ml-2" :class="buttonClass(button)" @click="update(button)">{{button}}</button>
        </div>

        <div class="wrapper">
          <Line id="chart" :data="chartData" :options="chartOptions" />
        </div>
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
        components: {
            Line
        },
        data() {
            return {
                records: [],
                currentButton: 'All',
                chartOptions: {
                    responsive: true,
                    maintainAspectRatio: false,
                    title: {
                        display: true,
                        text: 'Total Price (USD)'
                    },
                    plugins: {
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    elements: {
                        point: {
                            radius: 0
                        }
                    },
                    scales: {
                        x: {
                            type: 'time',
                            ticks: {
                                maxTicksLimit: 30
                            }
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
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: '#0074D9'
                        },
                        {
                            label: 'Original Price',
                            data: original,
                            pointRadius: 0,
                            pointHoverRadius: 5
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
        },
        mounted: async function () {
          const response = await fetch('/api/records')
          this.records = (await response.json()).data
        }
    }
</script>
<style scoped>
    .wrapper {
        height: 50vh;
    }
</style>