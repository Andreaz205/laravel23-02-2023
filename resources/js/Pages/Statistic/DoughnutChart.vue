<template>
    <Doughnut
        :options="chartOptions"
        :data="chartData"
    />
</template>

<script>
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js'
import { Doughnut } from 'vue-chartjs'
ChartJS.register(ArcElement, Tooltip, Legend)

export default {
    name: 'DoughnutChart',
    props: ['backgroundColors', 'chartDataFromBack', 'labelsProp'],
    components: {
        Doughnut
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
