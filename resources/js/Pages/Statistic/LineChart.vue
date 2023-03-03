
<template>
    <Line
        :options="chartOptions"
        :data="chartData"
    />
</template>

<script>
import { Line } from 'vue-chartjs'
import {Chart as ChartJS,
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    Title,
    Tooltip,
    Legend } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, PointElement,
    LineElement, CategoryScale, LinearScale)

export default {
    name: 'LineChart',
    components: { Line },
    props: ['labelsProp', 'chartDataFromBack', 'chartLabel', 'backgroundColor'],
    data() {
        return {
            chartData: {
                labels: this.labelsProp,
                datasets: [{
                    label: 'Статистика посещений за последний месяц',
                    backgroundColor: this.backgroundColor,
                    data: this.chartDataFromBack
                }]
            },
            chartOptions: {
                maintainAspectRatio: false,
                responsive: true
            }
        }
    }
}
</script>

