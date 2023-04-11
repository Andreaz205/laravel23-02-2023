<template>
    <div class="card">
        <div class="card-header text-center text-lg">
            Устройства пользователей за последний месяц
        </div>
        <div class="card-body">
            <PieChart
                :background-colors="['blue', 'red', 'yellow']"
                :chart-data-from-back="devicesData.count"
                :labels-prop="devicesData.labels"
            />
        </div>
    </div>
</template>

<script>
import PieChart from "@/Pages/Statistic/PieChart.vue";
export default {
    name: "DevicesChart",
    components: {PieChart},
    props: [
        'devicesData'
    ]
}
</script>

<style scoped>

</style>
