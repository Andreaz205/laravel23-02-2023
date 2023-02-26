
<template>
    <Bar
        id="my-chart-id"
        :options="chartOptions"
        :data="chartData"
    />
</template>

<script>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

export default {
    name: 'BarChart',
    components: { Bar },
    data() {
        return {
            chartData: {
                labels: [
                    '1', '2', '3', '4', '5', '6', '7', '8', '9', '10',
                    '11', '12', '13', '14', '15', '16', '17', '18', '19', '20',
                    '21', '22', '23', '24', '25', '26', '27', '28', '29', '30',
                ],
                datasets: [ { data: [
                        40, 20, 12, 40, 20, 12, 40, 20, 12, 40,
                        20, 12, 40, 20, 12, 40, 20, 12, 40, 20,
                        12, 40, 20, 12, 40, 20, 12, 40, 20, 12,
                    ] } ]
            },
            chartOptions: {
                responsive: true
            }
        }
    }
}
</script>



