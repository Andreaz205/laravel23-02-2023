<template>
    <div class="card">
        <div class="card-header text-center text-lg">
            Заказы
        </div>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <Errors :errors="errors" />
                        <Spinner v-if="isLoading"/>
                        <div class="row">
                        <label class="col-2">С</label>
                        <div class="col-10">
                            <VueDatePicker v-model="from" placeholder="Укажите дату начала ..." text-input />
                        </div>
                        </div>

                        <div class="row mt-2">
                        <label class="col-2">По</label>
                        <div class="col-10">
                            <VueDatePicker v-model="to" placeholder="Укажите дату окончания..." text-input />
                        </div>
                        </div>

                        <div class="row mt-3">
                        <label class="col-2">Интервал</label>
                        <div class="col-10">
                            <label for="radio-day">День</label>
                            <input type="radio" id='radio-day' v-model="interval" value="day">

                            <label for="radio-week">Неделя</label>
                            <input type="radio" id='radio-week' v-model="interval" value="week">

                            <label for="radio-month">Месяц</label>
                            <input type="radio" id='radio-month' v-model="interval" value="month">
                        </div>
                        </div>

                        <div class="text-center mt-3">
                        <button class="btn btn-primary" @click="handleChangePeriod">
                            Расчитать
                        </button>
                        </div>

                        <div class="mt-3">
                        <LineChart
                            :labels-prop="dateLabelsFinalData"
                            :chart-data-from-back="countDataFinalData"
                            background-color="#f87979"
                            chart-label="Статистика заказов"
                        />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import '@vuepic/vue-datepicker/dist/main.css'
import VueDatePicker from '@vuepic/vue-datepicker'
import LineChart from '../LineChart.vue'
import Spinner from "@/Components/Spinner.vue";
import Errors from "@/Components/Errors/Errors.vue";
export default {
    name: "OrdersDateCountChart",
    components: {Errors, Spinner, LineChart, VueDatePicker},
    props: ['dateLabels', 'countData'],
    data () {
        return {
            errors: null,
            dateLabelsFinalData: JSON.parse(JSON.stringify(this.dateLabels)),
            countDataFinalData: JSON.parse(JSON.stringify(this.countData)),
            interval: 'day',
            isLoading: false,
            from: JSON.parse(JSON.stringify(this.dateLabels[0])),
            to: JSON.parse(JSON.stringify(this.dateLabels[this.dateLabels.length - 1])) + ' 23:59'
        }
    },
    methods: {
        async handleChangePeriod() {
            try {
                this.isLoading = true
                let data = {
                    from: this.from,
                    to: this.to,
                    detailing: this.interval
                }

                let response = await axios.post(`/admin/statistics/calculate-orders-period`, data)
                this.dateLabelsFinalData = response.data.ordersDateLabels
                this.countDataFinalData = response.data.ordersCountData
                this.isLoading = false
            } catch (e) {
                this.isLoading = false
                if (e?.response?.status === 422) return this.errors = e.response.data.errors
                if (e?.response?.status === 500) return this.errors = [e.response.message]
                alert(e?.message ?? e)
            }
        }
    },
}
</script>

<style scoped>

</style>
