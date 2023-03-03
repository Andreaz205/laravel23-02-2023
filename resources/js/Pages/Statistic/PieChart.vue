<template>
    <Pie
        :options="chartOptions"
        :data="chartData"
    />
</template>

<script>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Pie } from 'vue-chartjs'

ChartJS.register(ArcElement, Tooltip, Legend)

export default {
    name: 'PieChart',
    props: ['backgroundColors', 'chartDataFromBack', 'labelsProp'],
    components: {
        Pie
    },
    data() {
        return {
            chartData: {
                labels: this.labelsProp,
                datasets: [{
                    backgroundColor: this.backgroundColors,
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
