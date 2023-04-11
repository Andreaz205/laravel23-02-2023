
<template>
    <Line
        :options="chartOptions"
        :data="chartData"
    />
</template>

<script>
import { Line } from 'vue-chartjs'
import {  Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend } from 'chart.js'

ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend
)

export default {
    name: 'LineChart',
    components: { Line },
    props: ['labelsProp', 'chartDataFromBack', 'chartLabel', 'backgroundColor'],
    data () {
        return {
            chartOptions: {
                maintainAspectRatio: false,
                responsive: true
            }
        }
    },
    computed: {
            chartData() {
                return {
                    labels: this.labelsProp,
                    datasets: [{
                        label: this.chartLabel ?? 'Заголовок',
                        backgroundColor: this.backgroundColor,
                        data: this.chartDataFromBack
                    }]
            }
        }
    }
}
</script>

