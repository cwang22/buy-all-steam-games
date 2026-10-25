<template>
    <div>
        <div class="chart-header">
            <div class="chart-title">
                <h2>{{ labels.title }}</h2>
                <ul class="legend">
                    <li><span class="swatch is-sale"></span>{{ labels.sale }}</li>
                    <li><span class="swatch is-original"></span>{{ labels.original }}</li>
                </ul>
            </div>
            <div v-if="records.length" class="segmented" role="group">
                <button v-for="(label, range) in labels.ranges" :key="range" type="button"
                        :aria-pressed="currentRange === range" @click="currentRange = range">{{ label }}</button>
            </div>
        </div>
        <div class="chart-wrapper">
            <Line v-if="records.length" :data="chartData" :options="chartOptions"/>
            <div v-else class="chart-status">{{ labels.loading }}</div>
        </div>
    </div>
</template>
<script>
    import {
        Chart as ChartJS,
        LinearScale,
        PointElement,
        LineElement,
        Filler,
        Tooltip,
        Legend,
        TimeScale,
    } from 'chart.js'
    import { Line } from 'vue-chartjs'
    import 'chartjs-adapter-date-fns'
    import { enUS, zhCN } from 'date-fns/locale'

    ChartJS.register(
        LinearScale,
        PointElement,
        LineElement,
        Filler,
        Tooltip,
        Legend,
        TimeScale,
    )

    const currency = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' })
    const compactCurrency = new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        notation: 'compact',
        maximumFractionDigits: 1,
    })
    const lightScheme = window.matchMedia('(prefers-color-scheme: light)')

    // Chart.js draws on a canvas, so it can't use the CSS variables directly.
    function readTheme() {
        const style = getComputedStyle(document.documentElement)
        const css = name => style.getPropertyValue(name).trim()

        return {
            text: css('--text'),
            muted: css('--muted'),
            border: css('--border'),
            surface: css('--surface-raised'),
            accent: css('--accent'),
            accentSoft: css('--accent-soft'),
        }
    }

    async function fetchRecords(url) {
        const response = await fetch(url)

        return (await response.json()).data.map(record => ({
            ...record,
            x: new Date(record.created_at)
        }))
    }

    export default {
        components: {
            Line
        },
        props: {
            labels: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                weekly: [],
                daily: null,
                dailyRequested: false,
                currentRange: 'all',
                theme: readTheme(),
            }
        },
        computed: {
            // "All" only needs one point per week, so the full daily history
            // is fetched only once a shorter range is picked.
            records() {
                return this.currentRange === 'all' ? this.weekly : (this.daily ?? this.weekly)
            },
            chartOptions() {
                const theme = this.theme

                return {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        // Drawn as HTML in the header instead.
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: theme.surface,
                            borderColor: theme.border,
                            borderWidth: 1,
                            titleColor: theme.text,
                            bodyColor: theme.text,
                            padding: 10,
                            callbacks: {
                                label: item => `${item.dataset.label}: ${currency.format(item.parsed.y)}`
                            }
                        }
                    },
                    scales: {
                        x: {
                            type: 'time',
                            adapters: {
                                date: {
                                    locale: document.documentElement.lang === 'zh' ? zhCN : enUS
                                }
                            },
                            grid: {
                                display: false
                            },
                            border: {
                                color: theme.border
                            },
                            ticks: {
                                color: theme.muted,
                                maxTicksLimit: 8,
                                maxRotation: 0
                            }
                        },
                        y: {
                            grid: {
                                color: theme.border
                            },
                            border: {
                                display: false
                            },
                            ticks: {
                                color: theme.muted,
                                callback: value => compactCurrency.format(value)
                            }
                        }
                    }
                }
            },
            chartData() {
                const since = new Date()
                if (this.currentRange === 'year') {
                    since.setFullYear(since.getFullYear() - 1)
                } else if (this.currentRange === 'month') {
                    since.setMonth(since.getMonth() - 1)
                }

                const records = this.currentRange === 'all'
                    ? this.records
                    : this.records.filter(record => record.x >= since)

                return {
                    datasets: [
                        {
                            label: this.labels.sale,
                            data: records.map(record => ({ x: record.x, y: record.sale })),
                            borderColor: this.theme.accent,
                            backgroundColor: this.theme.accentSoft,
                            borderWidth: 2,
                            fill: 'start',
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: this.theme.accent
                        },
                        {
                            label: this.labels.original,
                            data: records.map(record => ({ x: record.x, y: record.original })),
                            borderColor: this.theme.muted,
                            backgroundColor: this.theme.muted,
                            borderWidth: 1.5,
                            borderDash: [4, 4],
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: this.theme.muted
                        }
                    ]
                }
            }
        },
        watch: {
            async currentRange(range) {
                if (range !== 'all' && !this.dailyRequested) {
                    this.dailyRequested = true
                    this.daily = await fetchRecords('/api/records')
                }
            }
        },
        methods: {
            updateTheme() {
                this.theme = readTheme()
            }
        },
        mounted: async function () {
            lightScheme.addEventListener('change', this.updateTheme)

            this.weekly = await fetchRecords('/api/records?interval=week')
        },
        unmounted() {
            lightScheme.removeEventListener('change', this.updateTheme)
        }
    }
</script>
