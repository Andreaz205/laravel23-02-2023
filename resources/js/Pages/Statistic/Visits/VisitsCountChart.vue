<template>
    <div class="card m-3">
        <div class="card-header text-center text-lg">
            Посещения сайта
        </div>
        <div class="card-body">
        <BarChart
            :labels="dateLabelsFinalData"
            :chart-data-from-back="countDataFinalData"
            :background-color="'#3e9be7'"
            :chart-label="'Статистика посещений за последний месяц'"
        />
    </div>
    </div>

</template>

<script>

import BarChart from '../BarChart.vue'

export default {
    name: "VisitsCountChart",
    components: { BarChart},
    props: ['pagesVisits'],
    data () {
        return {
            dateLabelsFinalData: JSON.parse(JSON.stringify(this.pagesVisits.labels)),
            countDataFinalData: JSON.parse(JSON.stringify(this.pagesVisits.count)),
        }
    },
}
</script>

<style scoped>

</style>
