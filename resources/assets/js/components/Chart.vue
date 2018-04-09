<template>
    <div class="wrapper">
        <canvas id="chart"></canvas>
    </div>
</template>
<script>
    import moment from 'moment'
    import Chart from 'chart.js'

    export default {
        props: ['records'],
        mounted() {
            const sale = this.records.map(item => {
                    return {
                        x: moment(item.created_at),
                        y: parseFloat(item.sale.replace(',', ''))
                    }
                }
            )

            const original = this.records.map(item => {
                    return {
                        x: moment(item.created_at),
                        y: parseFloat(item.original.replace(',', ''))
                    }
                }
            )

            const ctx = document.getElementById('chart')

            new Chart(ctx, {
                type: 'line',
                data: {
                    datasets: [
                        {
                            label: 'Sale Price',
                            data: sale,
                            borderColor: '#0074D9',
                            backgroundColor: 'rgba(0, 116, 217, 0.2)'
                        },
                        {
                            label: 'Original Price',
                            data: original
                        }
                    ]
                },
                options: {
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
                        xAxes: [
                            {
                                type: 'time'
                            }
                        ],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            })
        }
    }
</script>
<style scoped>
    .wrapper {
        height: 50vh;
    }
</style>