
<template>
    <Bar
        :options="chartOptions"
        :data="chartData"
    />
</template>

<script>
import { Bar } from 'vue-chartjs'
import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    BarElement,
    CategoryScale,
    LinearScale
} from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
    name: 'BarChart',
    components: { Bar },
    props: ['labels', 'chartDataFromBack', 'chartLabel', 'backgroundColor'],
    data() {
        return {
            chartData: {
                labels: this.labels,
                datasets: [{
                    label: this.chartLabel,
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


