<template>
    <LineChart
        :labels-prop="dateLabels"
        :chart-data-from-back="countData"
        :background-color="'#f87979'"
        chart-label="Статистика заказов по дням"
    />
</template>

<script>
import LineChart from '../LineChart.vue'
export default {
    name: "OrdersDateCountChart",
    components: {LineChart},
    props: ['dateLabels', 'countData']
}
</script>

<style scoped>

</style>
