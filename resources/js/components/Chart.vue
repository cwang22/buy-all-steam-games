<template>
    <div>
        <div class="chart-header">
            <h2>{{ labels.title }}</h2>
            <ul class="legend">
                <li><span class="swatch is-sale"></span>{{ labels.sale }}</li>
                <li><span class="swatch is-original"></span>{{ labels.original }}</li>
            </ul>
        </div>
        <div class="chart-wrapper">
            <Line v-if="records.length" :data="chartData" :options="chartOptions"/>
            <div v-else class="chart-status">{{ labels.loading }}</div>
        </div>
        <div v-if="records.length" class="tabs" role="group">
            <button v-for="(label, range) in labels.ranges" :key="range" type="button"
                    :aria-pressed="currentRange === range" @click="currentRange = range">{{ label }}</button>
        </div>
    </div>
</template>
<script>
    import {
        Chart as ChartJS,
        LinearScale,
        PointElement,
        LineElement,
        Tooltip,
        TimeScale,
    } from 'chart.js'
    import { Line } from 'vue-chartjs'
    import 'chartjs-adapter-date-fns'
    import { enUS, zhCN } from 'date-fns/locale'

    ChartJS.register(
        LinearScale,
        PointElement,
        LineElement,
        Tooltip,
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
    const narrowScreen = window.matchMedia('(max-width: 640px)')

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
        }
    }

    async function fetchRecords(url) {
        const response = await fetch(url)

        return (await response.json()).data.map(record => ({
            ...record,
            x: new Date(record.created_at)
        }))
    }

    const NARROW_BUCKET_DAYS = 21

    // A phone has about a pixel per weekly point, which turns the sale line
    // into a sawtooth. Keep each bucket's cheapest record so big sales still dip.
    function bucketLows(records, days) {
        const lows = new Map()

        for (const record of records) {
            const bucket = Math.floor(record.x / (days * 86400000))

            if (!lows.has(bucket) || record.sale < lows.get(bucket).sale) {
                lows.set(bucket, record)
            }
        }

        return [...lows.values()]
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
                narrow: narrowScreen.matches,
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
                                maxTicksLimit: this.narrow ? 4 : 8,
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

                let records = this.currentRange === 'all'
                    ? this.records
                    : this.records.filter(record => record.x >= since)

                if (this.currentRange === 'all' && this.narrow) {
                    records = bucketLows(records, NARROW_BUCKET_DAYS)
                }

                return {
                    datasets: [
                        {
                            label: this.labels.sale,
                            data: records.map(record => ({ x: record.x, y: record.sale })),
                            borderColor: this.theme.accent,
                            backgroundColor: this.theme.accent,
                            borderWidth: 2.5,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: this.theme.accent
                        },
                        {
                            label: this.labels.original,
                            data: records.map(record => ({ x: record.x, y: record.original })),
                            borderColor: this.theme.text,
                            backgroundColor: this.theme.text,
                            borderWidth: 1.5,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: this.theme.text
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
            },
            updateNarrow(event) {
                this.narrow = event.matches
            }
        },
        mounted: async function () {
            lightScheme.addEventListener('change', this.updateTheme)
            narrowScreen.addEventListener('change', this.updateNarrow)

            this.weekly = await fetchRecords('/api/records?interval=week')
        },
        unmounted() {
            lightScheme.removeEventListener('change', this.updateTheme)
            narrowScreen.removeEventListener('change', this.updateNarrow)
        }
    }
</script>
