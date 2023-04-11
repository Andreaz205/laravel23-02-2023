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
    computed: {
        chartData() {
            return  {
                labels: this.labelsProp,
                datasets: [
                    {
                        backgroundColor: this.backgroundColors,
                        data: this.chartDataFromBack
                    }
                ]
            }
        },

        chartOptions() {
            return {
                maintainAspectRatio: false,
                responsive: true
            }
        }
    }
}
</script>
